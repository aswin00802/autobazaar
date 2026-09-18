<?php

namespace App\Http\Controllers\admin\vehicles;

use App\Http\Controllers\Controller;
use App\Models\Masters\AuthorizedSeller;
use App\Models\Masters\AutoBrand;
use App\Models\Masters\AutoFuelType;
use App\Models\Masters\AutoModel;
use App\Models\Vehicle\VehicleDocument;
use App\Models\Vehicle\VehicleFeature;
use App\Models\Vehicle\VehicleImage;
use App\Models\Vehicle\VehicleModel;
use App\Models\Vehicle\VehicleOffer;
use App\Models\Vehicle\VehiclePrice;
use App\Models\Vehicle\VehicleScore;
use App\Models\Vehicle\VehicleSpecification;
use App\Models\Vehicle\VehicleStock;
use App\Models\Vehicle\VehicleSuitability;
use App\Models\Vehicle\VehicleVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Vehicle catalogue: one marketed model with every child block the
 * detail page shows (variants, specs, prices, scores, images ...).
 */
class VehicleModelsController extends Controller
{
    public const SEGMENTS = ['passenger' => 'Passenger', 'cargo' => 'Cargo'];
    public const FUEL_KEYS = ['petrol' => 'Petrol', 'cng' => 'CNG', 'lpg' => 'LPG', 'diesel' => 'Diesel', 'electric' => 'Electric'];
    public const VARIANT_ICONS = ['fuel' => 'Fuel', 'gas' => 'Gas', 'bolt' => 'Bolt'];
    public const SCORE_TONES = ['brand' => 'Brand', 'accent' => 'Accent', 'info' => 'Info', 'violet' => 'Violet'];
    public const MAINTENANCE = ['Low', 'Medium', 'High'];

    public function __construct()
    {
        $this->middleware(['permission:vehicle_catalog'])->only(['index']);
        $this->middleware(['permission:add_vehicle_catalog'])->only(['create', 'store']);
        $this->middleware(['permission:edit_vehicle_catalog'])->only(['edit', 'update', 'statusToggle', 'deleteImage', 'deleteDocument']);
        $this->middleware(['permission:delete_vehicle_catalog'])->only(['delete']);
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $models = VehicleModel::with(['brand', 'prices' => fn ($q) => $q->orderByDesc('is_default')->orderBy('id')])
            ->withCount('variants')
            ->when($status === 'live', fn ($q) => $q->where('status_id', 1))
            ->when($status === 'draft', fn ($q) => $q->where('status_id', 0))
            ->orderBy('status_id', 'desc')->orderBy('sort_order')->orderBy('name')
            ->get();

        return view('admin.vehicles.catalogue.index', compact('models', 'status'));
    }

    public function create()
    {
        return view('admin.vehicles.catalogue.create', $this->formData(null));
    }

    public function store(Request $request)
    {
        try {
            $this->validateModel($request);

            if (VehicleModel::where('slug', $request->slug)->exists()) {
                return redirect()->route('vehicles.catalogue.create')->withInput()->with('error', 'This slug already exists');
            }
            if (VehicleModel::where('auto_brand_id', $request->auto_brand_id)->where('model_slug', $request->model_slug)->exists()) {
                return redirect()->route('vehicles.catalogue.create')->withInput()->with('error', 'This model slug already exists for the selected brand');
            }

            DB::transaction(function () use ($request) {
                $model = new VehicleModel();
                $this->fill($model, $request);
                $model->created_by = Auth::user()->id;
                $model->ip_address = $request->ip();
                $model->save();
                $this->syncChildren($model, $request);
            });

            return redirect()->route('vehicles.catalogue')->with('success', 'Vehicle model created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('vehicles.catalogue.create')->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->route('vehicles.catalogue.create')->withInput()->withErrors($e->getMessage());
        }
    }

    public function edit($id)
    {
        $model = VehicleModel::with([
            'variants', 'specifications', 'images', 'prices', 'scores', 'features',
            'suitability', 'offers', 'documents', 'stock',
        ])->findOrFail($id);

        return view('admin.vehicles.catalogue.edit', $this->formData($model));
    }

    public function update(Request $request, $id)
    {
        $model = VehicleModel::findOrFail($id);

        try {
            $this->validateModel($request);

            if (VehicleModel::where('slug', $request->slug)->where('id', '!=', $id)->exists()) {
                return redirect()->back()->withInput()->with('error', 'This slug already exists');
            }
            if (VehicleModel::where('auto_brand_id', $request->auto_brand_id)->where('model_slug', $request->model_slug)->where('id', '!=', $id)->exists()) {
                return redirect()->back()->withInput()->with('error', 'This model slug already exists for the selected brand');
            }

            DB::transaction(function () use ($model, $request) {
                $this->fill($model, $request);
                $model->ip_address = $request->ip();
                $model->save();
                $this->syncChildren($model, $request);
            });

            return redirect()->route('vehicles.catalogue')->with('success', 'Vehicle model updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('vehicles.catalogue.edit', $id)->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->route('vehicles.catalogue.edit', $id)->withInput()->withErrors($e->getMessage());
        }
    }

    /** Hard delete: the model and every child row and file. */
    public function delete(Request $request)
    {
        $model = VehicleModel::find($request->id);
        if (! $model) {
            return response()->json(['success' => false, 'message' => 'Vehicle model not found']);
        }

        DB::transaction(function () use ($model) {
            foreach (VehicleImage::where('vehicle_model_id', $model->id)->get() as $img) {
                $this->unlinkFile($img->image);
                $img->delete();
            }
            foreach (VehicleDocument::where('vehicle_model_id', $model->id)->get() as $doc) {
                $this->unlinkFile($doc->file);
                $doc->delete();
            }
            foreach ([VehicleVariant::class, VehicleSpecification::class, VehiclePrice::class, VehicleScore::class,
                VehicleFeature::class, VehicleSuitability::class, VehicleOffer::class, VehicleStock::class] as $class) {
                $class::where('vehicle_model_id', $model->id)->delete();
            }
            $this->unlinkFile($model->image);
            $model->delete();
        });

        return response()->json(['success' => true, 'message' => 'Vehicle model deleted successfully']);
    }

    /** 0 draft <-> 1 live */
    public function statusToggle(Request $request)
    {
        $model = VehicleModel::find($request->id);
        if (! $model) {
            return response()->json(['success' => false, 'message' => 'Vehicle model not found'], 404);
        }
        $model->status_id = $request->has('status_id') ? (int) $request->status_id : ($model->status_id == 1 ? 0 : 1);
        $model->save();

        return response()->json(['success' => true, 'status_id' => $model->status_id]);
    }

    public function deleteImage(Request $request)
    {
        $image = VehicleImage::find($request->id);
        if (! $image) {
            return response()->json(['success' => false, 'message' => 'Image not found']);
        }
        $this->unlinkFile($image->image);
        $image->delete();

        return response()->json(['success' => true, 'message' => 'Image deleted']);
    }

    public function deleteDocument(Request $request)
    {
        $doc = VehicleDocument::find($request->id);
        if (! $doc) {
            return response()->json(['success' => false, 'message' => 'Document not found']);
        }
        $this->unlinkFile($doc->file);
        $doc->delete();

        return response()->json(['success' => true, 'message' => 'Document deleted']);
    }

    /* ------------------------------------------------------------------ */

    private function formData(?VehicleModel $model): array
    {
        return [
            'model'        => $model,
            'brands'       => AutoBrand::where('status', 1)->orderBy('brand_name')->get(['id', 'brand_name']),
            'masterModels' => AutoModel::where('status_id', 1)->orderBy('model_name')->get(['id', 'brand_id', 'model_name']),
            'fuels'        => AutoFuelType::where('status', 1)->get(['id', 'name']),
            'dealers'      => AuthorizedSeller::where('status_id', '!=', 3)->orderBy('dealer_name')->get(['id', 'dealer_name', 'location']),
            'segments'     => self::SEGMENTS,
            'fuelKeys'     => self::FUEL_KEYS,
            'variantIcons' => self::VARIANT_ICONS,
            'scoreTones'   => self::SCORE_TONES,
            'maintenance'  => self::MAINTENANCE,
            'docTypes'     => VehicleDocument::TYPES,
        ];
    }

    private function validateModel(Request $request): void
    {
        $request->validate([
            'auto_brand_id'      => 'required|integer',
            'auto_model_id'      => 'nullable|integer',
            'name'               => 'required|string|max:255',
            'slug'               => 'required|string|max:120|regex:/^[a-z0-9-]+$/',
            'model_slug'         => 'required|string|max:80|regex:/^[a-z0-9-]+$/',
            'tagline'            => 'nullable|string|max:255',
            'badge'              => 'nullable|string|max:40',
            'segment'            => 'required|in:' . implode(',', array_keys(self::SEGMENTS)),
            'seating'            => 'required|integer|min:1|max:20',
            'maintenance_level'  => 'nullable|string|max:20',
            'best_for'           => 'nullable|string|max:60',
            'sort_order'         => 'nullable|integer',
            'status_id'          => 'required|in:0,1',
            'meta_title'         => 'nullable|string|max:255',
            'meta_description'   => 'nullable|string|max:255',
            'image'              => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'score_overall'      => 'nullable|numeric|min:0|max:10',
            'score_rank_note'    => 'nullable|string|max:80',
            'score_summary'      => 'nullable|string|max:255',
            'dealer_id'          => 'nullable|integer',
            'warranty_years'     => 'nullable|integer|min:0|max:99',
            'warranty_km'        => 'nullable|integer|min:0',
            'engine_warranty_years' => 'nullable|integer|min:0|max:99',
            'engine_warranty_km' => 'nullable|integer|min:0',
            'service_interval_km' => 'nullable|integer|min:0',
            'service_interval_months' => 'nullable|integer|min:0|max:99',
            'free_services'      => 'nullable|integer|min:0|max:99',
            'free_services_km'   => 'nullable|integer|min:0',

            'variants'                      => 'nullable|array',
            'variants.*.name'               => 'required_with:variants|string|max:60',
            'variants.*.fuel_key'           => 'nullable|in:' . implode(',', array_keys(self::FUEL_KEYS)),
            'variants.*.ex_showroom_price'  => 'nullable|numeric|min:0',
            'variants.*.mileage'            => 'nullable|numeric|min:0',
            'specifications'                => 'nullable|array',
            'specifications.*.label'        => 'required_with:specifications|string|max:80',
            'specifications.*.value'        => 'required_with:specifications|string|max:255',
            'scores'                        => 'nullable|array',
            'scores.*.label'                => 'required_with:scores|string|max:60',
            'scores.*.score'                => 'nullable|numeric|min:0|max:10',
            'features'                      => 'nullable|array',
            'features.*.label'              => 'required_with:features|string|max:80',
            'offers'                        => 'nullable|array',
            'offers.*.title'                => 'required_with:offers|string|max:255',
            'offers.*.value_amount'         => 'nullable|numeric|min:0',
            'offers.*.valid_from'           => 'nullable|date',
            'offers.*.valid_to'             => 'nullable|date',
            'prices'                        => 'nullable|array',
            'prices.*.location'             => 'required_with:prices|string|max:80',
            'prices.*.ex_showroom'          => 'nullable|numeric|min:0',
            'stock'                         => 'nullable|array',
            'stock.*.qty'                   => 'nullable|integer|min:0',
            'images'                        => 'nullable|array',
            'images.*'                      => 'image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'documents'                     => 'nullable|array',
            'documents.*.title'             => 'nullable|string|max:255',
            'documents.*.type'              => 'nullable|in:' . implode(',', array_keys(VehicleDocument::TYPES)),
            'documents.*.file'              => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ], [
            'slug.regex'       => 'Slug may only contain lowercase letters, numbers and dashes.',
            'model_slug.regex' => 'Model slug may only contain lowercase letters, numbers and dashes.',
        ]);
    }

    private function fill(VehicleModel $model, Request $request): void
    {
        $model->auto_brand_id    = $request->auto_brand_id;
        $model->auto_model_id    = $request->auto_model_id ?: null;
        $model->name             = trim($request->name);
        $model->slug             = Str::slug($request->slug);
        $model->model_slug       = Str::slug($request->model_slug);
        $model->tagline          = $request->tagline;
        $model->description      = $request->description;
        $model->badge            = $request->badge ?: null;
        $model->segment          = $request->segment;
        $model->seating          = (int) $request->seating;
        $model->use_case         = $this->csvToArray($request->use_case);
        $model->maintenance_level = $request->maintenance_level ?: 'Low';
        $model->best_for         = $request->best_for;
        $model->is_popular       = $request->has('is_popular') ? 1 : 0;
        $model->sort_order       = (int) ($request->sort_order ?? 0);
        $model->status_id        = (int) $request->status_id;
        $model->meta_title       = $request->meta_title;
        $model->meta_description = $request->meta_description;
        $model->score_overall    = (float) ($request->score_overall ?? 0);
        $model->score_rank_note  = $request->score_rank_note;
        $model->score_summary    = $request->score_summary;
        $model->dealer_id        = $request->dealer_id ?: null;

        foreach (['warranty_years', 'warranty_km', 'engine_warranty_years', 'engine_warranty_km',
            'service_interval_km', 'service_interval_months', 'free_services', 'free_services_km'] as $f) {
            $model->{$f} = $request->filled($f) ? (int) $request->{$f} : null;
        }

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $this->unlinkFile($model->image);
            $model->image = $this->moveUpload($request->file('image'), 'uploads/vehicles');
        }
    }

    /**
     * Rewrite every child collection from the submitted arrays. Variants are
     * upserted by id so enquiries/stock keep pointing at the same variant row;
     * the other lists are cheap to delete and reinsert. Images and documents
     * are only ever appended here (deleted one by one via ajax).
     */
    private function syncChildren(VehicleModel $model, Request $request): void
    {
        $mid = $model->id;

        // Variants: upsert by id, delete rows no longer submitted.
        $variantMap = [];   // "new:<idx>" or existing id => id
        $keepIds = [];
        $default = $request->input('variant_default');   // row index of the default variant
        foreach ($this->rows($request->input('variants', [])) as $idx => $row) {
            if (trim((string) ($row['name'] ?? '')) === '') {
                continue;
            }
            $data = [
                'vehicle_model_id'  => $mid,
                'name'              => trim($row['name']),
                'fuel_key'          => $row['fuel_key'] ?? 'petrol',
                'fuel_type_id'      => ! empty($row['fuel_type_id']) ? (int) $row['fuel_type_id'] : null,
                'icon'              => $row['icon'] ?? 'fuel',
                'engine_cc'         => $row['engine_cc'] ?? null,
                'power'             => $row['power'] ?? null,
                'mileage'           => ($row['mileage'] ?? '') !== '' ? (float) $row['mileage'] : null,
                'mileage_unit'      => ($row['mileage_unit'] ?? '') ?: 'km/litre',
                'payload_kg'        => ($row['payload_kg'] ?? '') !== '' ? (int) $row['payload_kg'] : null,
                'transmission'      => $row['transmission'] ?? null,
                'ex_showroom_price' => (float) ($row['ex_showroom_price'] ?? 0),
                'is_default'        => ((string) $default === (string) $idx) ? 1 : 0,
                'sort_order'        => (int) $idx,
                'status_id'         => 1,
            ];
            $existing = ! empty($row['id']) ? VehicleVariant::where('vehicle_model_id', $mid)->find($row['id']) : null;
            if ($existing) {
                $existing->update($data);
                $variant = $existing;
            } else {
                $variant = VehicleVariant::create($data);
            }
            $keepIds[] = $variant->id;
            $variantMap['new:' . $idx] = $variant->id;
            $variantMap[(string) $variant->id] = $variant->id;
        }
        VehicleVariant::where('vehicle_model_id', $mid)->whereNotIn('id', $keepIds ?: [0])->delete();
        // Guarantee exactly one default when variants exist.
        if ($keepIds && ! VehicleVariant::where('vehicle_model_id', $mid)->where('is_default', 1)->exists()) {
            VehicleVariant::where('vehicle_model_id', $mid)->orderBy('sort_order')->first()->update(['is_default' => 1]);
        }
        $resolveVariant = fn ($v) => ($v !== null && $v !== '' && isset($variantMap[(string) $v])) ? $variantMap[(string) $v] : null;

        // Specifications
        VehicleSpecification::where('vehicle_model_id', $mid)->delete();
        foreach ($this->rows($request->input('specifications', [])) as $idx => $row) {
            if (trim((string) ($row['label'] ?? '')) === '') {
                continue;
            }
            VehicleSpecification::create([
                'vehicle_model_id' => $mid,
                'spec_group'       => trim($row['spec_group'] ?? '') ?: 'General',
                'label'            => trim($row['label']),
                'value'            => trim((string) ($row['value'] ?? '')),
                'is_key'           => ! empty($row['is_key']) ? 1 : 0,
                'sort_order'       => (int) $idx,
            ]);
        }

        // Scores
        VehicleScore::where('vehicle_model_id', $mid)->delete();
        foreach ($this->rows($request->input('scores', [])) as $idx => $row) {
            if (trim((string) ($row['label'] ?? '')) === '') {
                continue;
            }
            VehicleScore::create([
                'vehicle_model_id' => $mid,
                'label'            => trim($row['label']),
                'score'            => (float) ($row['score'] ?? 0),
                'tone'             => $row['tone'] ?? 'brand',
                'sort_order'       => (int) $idx,
            ]);
        }

        // Features
        VehicleFeature::where('vehicle_model_id', $mid)->delete();
        foreach ($this->rows($request->input('features', [])) as $idx => $row) {
            if (trim((string) ($row['label'] ?? '')) === '') {
                continue;
            }
            VehicleFeature::create([
                'vehicle_model_id' => $mid,
                'icon'             => trim($row['icon'] ?? '') ?: 'check',
                'label'            => trim($row['label']),
                'sort_order'       => (int) $idx,
            ]);
        }

        // Suitability: one label per line
        VehicleSuitability::where('vehicle_model_id', $mid)->delete();
        foreach (['suitable' => 'suitable_lines', 'not_recommended' => 'not_recommended_lines'] as $type => $field) {
            foreach ($this->lines($request->input($field)) as $idx => $label) {
                VehicleSuitability::create([
                    'vehicle_model_id' => $mid,
                    'type'             => $type,
                    'label'            => Str::limit($label, 80, ''),
                    'sort_order'       => $idx,
                ]);
            }
        }

        // Offers
        VehicleOffer::where('vehicle_model_id', $mid)->delete();
        foreach ($this->rows($request->input('offers', [])) as $idx => $row) {
            if (trim((string) ($row['title'] ?? '')) === '') {
                continue;
            }
            VehicleOffer::create([
                'vehicle_model_id' => $mid,
                'title'            => trim($row['title']),
                'value_amount'     => ($row['value_amount'] ?? '') !== '' ? (float) $row['value_amount'] : null,
                'valid_from'       => ($row['valid_from'] ?? '') ?: null,
                'valid_to'         => ($row['valid_to'] ?? '') ?: null,
                'sort_order'       => (int) $idx,
                'status_id'        => ! empty($row['status_id']) ? 1 : 0,
                'created_by'       => Auth::id(),
            ]);
        }

        // Prices (rows only, no files)
        VehiclePrice::where('vehicle_model_id', $mid)->delete();
        $priceDefault = $request->input('price_default');
        $priceRows = $this->rows($request->input('prices', []));
        $first = true;
        foreach ($priceRows as $idx => $row) {
            if (trim((string) ($row['location'] ?? '')) === '') {
                continue;
            }
            VehiclePrice::create([
                'vehicle_model_id'   => $mid,
                'vehicle_variant_id' => $resolveVariant($row['vehicle_variant_id'] ?? null),
                'location'           => trim($row['location']),
                'state'              => trim($row['state'] ?? '') ?: 'Tamil Nadu',
                'ex_showroom'        => (float) ($row['ex_showroom'] ?? 0),
                'rto'                => (float) ($row['rto'] ?? 0),
                'insurance'          => (float) ($row['insurance'] ?? 0),
                'registration'       => (float) ($row['registration'] ?? 0),
                'other'              => (float) ($row['other'] ?? 0),
                'accessories'        => (float) ($row['accessories'] ?? 0),
                'is_default'         => ($priceDefault !== null ? (string) $priceDefault === (string) $idx : $first) ? 1 : 0,
                'status_id'          => 1,
            ]);
            $first = false;
        }

        // Stock
        VehicleStock::where('vehicle_model_id', $mid)->delete();
        foreach ($this->rows($request->input('stock', [])) as $row) {
            $hasSomething = ($row['colour'] ?? '') !== '' || ($row['qty'] ?? '') !== '' || ! empty($row['vehicle_variant_id']) || ! empty($row['dealer_id']);
            if (! $hasSomething) {
                continue;
            }
            VehicleStock::create([
                'vehicle_model_id'   => $mid,
                'vehicle_variant_id' => $resolveVariant($row['vehicle_variant_id'] ?? null),
                'dealer_id'          => ! empty($row['dealer_id']) ? (int) $row['dealer_id'] : null,
                'colour'             => $row['colour'] ?? null,
                'qty'                => (int) ($row['qty'] ?? 0),
                'delivery_days_min'  => (int) (($row['delivery_days_min'] ?? '') ?: 1),
                'delivery_days_max'  => (int) (($row['delivery_days_max'] ?? '') ?: 3),
                'status_id'          => 1,
            ]);
        }

        // Images: append
        if ($request->hasFile('images')) {
            $sort = (int) VehicleImage::where('vehicle_model_id', $mid)->max('sort_order');
            foreach ($request->file('images') as $file) {
                if ($file && $file->isValid()) {
                    VehicleImage::create([
                        'vehicle_model_id' => $mid,
                        'image'            => $this->moveUpload($file, 'uploads/vehicles'),
                        'sort_order'       => ++$sort,
                        'status_id'        => 1,
                    ]);
                }
            }
        }

        // Documents: append
        $sort = (int) VehicleDocument::where('vehicle_model_id', $mid)->max('sort_order');
        foreach ($this->rows($request->input('documents', [])) as $idx => $row) {
            $file = $request->file("documents.$idx.file");
            if (! $file || ! $file->isValid()) {
                continue;
            }
            VehicleDocument::create([
                'vehicle_model_id' => $mid,
                'type'             => $row['type'] ?? 'brochure',
                'title'            => trim($row['title'] ?? '') ?: (VehicleDocument::TYPES[$row['type'] ?? 'brochure'] ?? 'Document'),
                'file'             => $this->moveUpload($file, 'uploads/vehicles/docs'),
                'sort_order'       => ++$sort,
                'status_id'        => 1,
            ]);
        }
    }

    /** Submitted repeatable rows in form order (keys may be sparse after JS removes). */
    private function rows($input): array
    {
        if (! is_array($input)) {
            return [];
        }
        ksort($input, SORT_NATURAL);

        return array_values(array_filter($input, 'is_array'));
    }

    private function lines($text): array
    {
        $out = [];
        foreach (preg_split('/\r\n|\r|\n/', (string) $text) as $line) {
            $line = trim($line);
            if ($line !== '') {
                $out[] = $line;
            }
        }

        return $out;
    }

    private function csvToArray($csv): ?array
    {
        $items = array_values(array_filter(array_map('trim', explode(',', (string) $csv)), fn ($v) => $v !== ''));

        return $items ?: null;
    }

    private function moveUpload($file, string $folder): string
    {
        $destination = public_path($folder);
        if (! file_exists($destination)) {
            mkdir($destination, 0777, true);
        }
        $name = uniqid() . '.' . strtolower($file->getClientOriginalExtension());
        $file->move($destination, $name);

        return $folder . '/' . $name;
    }

    /** Remove an uploaded file; seeded brand assets under assets/ are never touched. */
    private function unlinkFile(?string $path): void
    {
        if ($path && str_starts_with($path, 'uploads/') && file_exists(public_path($path))) {
            @unlink(public_path($path));
        }
    }
}
