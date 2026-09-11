<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\spareparts\Product;
use App\Models\spareparts\SparepartsSubCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Customer-facing AutoBazaar website.
 *
 * Accessories pages read the live catalogue (products / product_brand_models).
 * Vehicle, offer, scheme, news and account pages still read resources/fixtures
 * until their tables and admin modules are built.
 */
class SiteController extends Controller
{
    /** Fixture files, memoised per request. */
    private array $cache = [];

    private function fixture(string $name): array
    {
        return $this->cache[$name] ??= require resource_path("fixtures/{$name}.php");
    }

    /** Data every page needs: chrome, nav, contact, location switcher. */
    private function shared(): array
    {
        return [
            'site' => $this->fixture('site'),
            'locations' => $this->fixture('locations'),
        ];
    }

    private function vehicles(): array
    {
        return $this->fixture('vehicles');
    }

    private function vehicle(string $slug): ?array
    {
        return Arr::first($this->vehicles(), fn ($v) => $v['slug'] === $slug);
    }

    /* ==================================================================== home */

    public function home()
    {
        $content = $this->fixture('content');

        return view('site.home', [
            ...$this->shared(),
            'vehicles' => array_slice($this->vehicles(), 0, 5),
            'offers' => $content['offers'],
            // Spread across categories. Taking the first six by id gave six
            // near-identical floor mats, which read as one repeated tile.
            'accessories' => $this->featuredAccessories(6),
        ]);
    }

    /* ================================================================ vehicles */

    public function newAutos()
    {
        return view('site.vehicles.index', [
            ...$this->shared(),
            'title' => 'New Autos',
            'lede' => 'Explore every autorickshaw model — compare specs, check on-road price and calculate your EMI.',
            'vehicles' => $this->vehicles(),
            'condition' => 'new',
        ]);
    }

    public function usedAutos()
    {
        // The used tree is a listing product, not a catalogue one — the cards
        // carry year/km/owner rather than variants and scores.
        return view('site.vehicles.used', [
            ...$this->shared(),
            'title' => 'Used Autos',
            'lede' => 'Verified pre-owned autorickshaws with documented RC, FC and permit status.',
            'vehicles' => $this->vehicles(),
        ]);
    }

    public function brand(string $brand)
    {
        $vehicles = array_values(array_filter(
            $this->vehicles(),
            fn ($v) => $v['brand_slug'] === $brand,
        ));

        abort_if(empty($vehicles), 404);

        return view('site.vehicles.index', [
            ...$this->shared(),
            'title' => $vehicles[0]['brand'] . ' Autorickshaws',
            'lede' => 'All ' . $vehicles[0]['brand'] . ' models available through AutoBazaar.',
            'vehicles' => $vehicles,
            'condition' => 'new',
            'brand' => $vehicles[0],
        ]);
    }

    public function model(string $brand, string $model)
    {
        // URLs read /new-autos/tvs/king-deluxe, so resolve on brand + the
        // brand-relative model slug rather than the globally unique one.
        $vehicle = Arr::first(
            $this->vehicles(),
            fn ($v) => $v['brand_slug'] === $brand && $v['model_slug'] === $model,
        );

        abort_if(! $vehicle, 404);

        $similar = array_values(array_filter(
            $this->vehicles(),
            fn ($v) => $v['slug'] !== $vehicle['slug'],
        ));

        return view('site.vehicles.show', [
            ...$this->shared(),
            'vehicle' => $vehicle,
            'similar' => array_slice($similar, 0, 4),
        ]);
    }

    /* ================================================================= compare */

    public function compare(?string $combo = null)
    {
        $all = $this->vehicles();

        // /compare/tvs-king-deluxe-vs-bajaj-re -> the named models, else a
        // sensible default set that mirrors compare.jpeg.
        $selected = $combo
            ? array_values(array_filter(array_map(
                fn ($slug) => $this->vehicle($slug),
                explode('-vs-', $combo),
            )))
            : array_values(array_filter([
                $this->vehicle('tvs-king-deluxe'),
                $this->vehicle('bajaj-re'),
                $this->vehicle('piaggio-ape-xtra'),
                $this->vehicle('mahindra-treo-plus'),
            ]));

        return view('site.compare', [
            ...$this->shared(),
            'all' => $all,
            'selected' => array_slice($selected, 0, 4),
        ]);
    }

    /* ========================================================= buying journey */

    public function buyingOptions()
    {
        return view('site.buying-options', $this->shared());
    }

    public function enquiry(?string $model = null)
    {
        return view('site.enquiry', [
            ...$this->shared(),
            'vehicle' => $this->vehicle($model ?? 'tvs-king-deluxe') ?? $this->vehicles()[0],
            'vehicles' => $this->vehicles(),
        ]);
    }

    /* ================================================================ content */

    public function offers()
    {
        return view('site.offers', [
            ...$this->shared(),
            'offers' => $this->fixture('content')['offers'],
            'vehicles' => $this->vehicles(),
        ]);
    }

    public function finance()
    {
        return view('site.finance', [
            ...$this->shared(),
            'vehicles' => $this->vehicles(),
        ]);
    }

    public function schemes()
    {
        $content = $this->fixture('content');

        return view('site.schemes', [
            ...$this->shared(),
            'schemes' => $content['schemes'],
            'categories' => $content['scheme_categories'],
            'steps' => $content['scheme_steps'],
        ]);
    }

    public function news()
    {
        $content = $this->fixture('content');

        return view('site.news.index', [
            ...$this->shared(),
            'posts' => $content['news'],
            'categories' => $content['news_categories'],
        ]);
    }

    public function newsArticle(string $slug)
    {
        $content = $this->fixture('content');
        $post = Arr::first($content['news'], fn ($p) => $p['slug'] === $slug);

        abort_if(! $post, 404);

        return view('site.news.show', [
            ...$this->shared(),
            'post' => $post,
            'related' => array_slice(array_values(array_filter(
                $content['news'],
                fn ($p) => $p['slug'] !== $slug,
            )), 0, 3),
        ]);
    }

    public function app()
    {
        return view('site.app', $this->shared());
    }

    /* ============================================================ accessories */

    /**
     * The accessories shop reads the REAL catalogue (`products` joined to
     * `product_brand_models` for pricing), not fixtures — the client has 59
     * live products with real prices.
     *
     * Two things the live data does not have, and which are therefore absent
     * rather than invented: ratings/review counts (no review system exists
     * yet) and product photography (the paths in `products.image` do not
     * resolve on disk, so each product falls back to its drawn placeholder).
     */
    private function liveProducts(): array
    {
        // Subcategory slug -> placeholder illustration.
        $art = ['floor-mats' => 'mat', 'auto-cover' => 'cover', 'rain-cutters' => 'curtain'];

        return Product::query()
            ->where('status_id', 1)
            ->with(['subCategory', 'productBrandModel.autoBrands'])
            ->orderBy('id')
            ->get()
            ->map(function (Product $p) use ($art) {
                $variant = $p->productBrandModel->first();
                if (! $variant) {
                    return null; // No priced variant — nothing to sell.
                }

                $mrp = (float) $variant->price;
                $price = (float) ($variant->offer_price ?: $variant->price);
                $slug = $p->subCategory->slug ?? 'other';

                return [
                    'id' => 'p' . $p->id,
                    'product_model_id' => $variant->id,
                    'name' => trim(preg_replace('/\s+/', ' ', str_replace('?', '–', $p->name))),
                    'subtitle' => $p->subCategory->name ?? null,
                    'meta' => $p->description ? Str::limit(strip_tags($p->description), 60) : null,
                    'category' => $slug,
                    'brand' => $variant->autoBrands->brand_name ?? 'Generic',
                    'price' => (int) round($price),
                    'mrp' => (int) round($mrp),
                    'rating' => null,
                    'reviews' => null,
                    'badge' => $price < $mrp ? 'Offer' : null,
                    'popularity' => $p->id,
                    'art' => $art[$slug] ?? 'grid',
                    'image' => $this->resolveImage($p->image),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Returns the stored path only when the file is actually on disk.
     *
     * NOTE: as of this environment none of the 129 image paths in `products`
     * or `product_brand_model_images` resolve — the DB appears to have been
     * copied without public/uploads. Products therefore fall back to a drawn
     * placeholder. Restore the upload folder and they light up automatically.
     *
     * The directory is listed ONCE per request and checked in memory, rather
     * than a File::exists() stat per product (59 stats on every page load).
     */
    private function resolveImage(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $dir = dirname($path);

        $this->cache['files'][$dir] ??= File::isDirectory(public_path($dir))
            ? array_flip(scandir(public_path($dir)) ?: [])
            : [];

        return isset($this->cache['files'][$dir][basename($path)]) ? $path : null;
    }

    /**
     * A varied handful for the home rail: round-robin across the categories
     * that actually have stock, rather than the first N by id.
     */
    private function featuredAccessories(int $limit): array
    {
        $byCategory = collect($this->liveProducts())->groupBy('category');
        $picked = [];

        // Take one from each category in turn until we have enough.
        for ($round = 0; count($picked) < $limit && $round < 20; $round++) {
            foreach ($byCategory as $group) {
                if (isset($group[$round])) {
                    $picked[] = $group[$round];
                }
                if (count($picked) >= $limit) {
                    break;
                }
            }
        }

        return $picked;
    }

    /** Subcategory chips, counted from live products. */
    private function liveCategories(array $products): array
    {
        $counts = collect($products)->countBy('category');

        $categories = SparepartsSubCategories::query()
            ->whereIn('slug', $counts->keys())
            ->get()
            ->map(fn ($s) => [
                'slug' => $s->slug,
                'name' => $s->name,
                'art' => ['floor-mats' => 'mat', 'auto-cover' => 'cover', 'rain-cutters' => 'curtain'][$s->slug] ?? 'grid',
                'count' => $counts[$s->slug] ?? 0,
            ])
            ->sortByDesc('count')
            ->values()
            ->all();

        return [
            ['slug' => 'all', 'name' => 'All Accessories', 'art' => 'grid', 'count' => count($products)],
            ...$categories,
        ];
    }

    private function liveBrands(array $products): array
    {
        return collect($products)
            ->countBy('brand')
            ->map(fn ($count, $name) => ['name' => $name, 'count' => $count])
            ->sortByDesc('count')
            ->values()
            ->all();
    }

    public function accessories()
    {
        $products = $this->liveProducts();

        return view('site.accessories.index', [
            ...$this->shared(),
            'categories' => $this->liveCategories($products),
            'products' => array_slice($products, 0, 6),
            // Promo banners stay editorial — there is no CMS for them yet.
            'promos' => $this->fixture('accessories')['promos'],
        ]);
    }

    public function shop(Request $request)
    {
        $products = $this->liveProducts();

        return view('site.accessories.shop', [
            ...$this->shared(),
            'categories' => $this->liveCategories($products),
            'brands' => $this->liveBrands($products),
            'products' => $products,
            'activeCategory' => $request->query('category', 'all'),
        ]);
    }


    /* ================================================================ account */

    public function account()
    {
        $commerce = $this->fixture('commerce');

        return view('site.account.dashboard', [
            ...$this->shared(),
            'account' => $commerce['account'],
            'order' => $commerce['order'],
        ]);
    }

    public function orders()
    {
        $commerce = $this->fixture('commerce');

        return view('site.account.orders', [
            ...$this->shared(),
            'account' => $commerce['account'],
            'order' => $commerce['order'],
        ]);
    }

    public function order(string $id)
    {
        $commerce = $this->fixture('commerce');

        return view('site.account.order', [
            ...$this->shared(),
            'account' => $commerce['account'],
            'order' => $commerce['order'],
        ]);
    }

    public function accountSection(string $section)
    {
        $commerce = $this->fixture('commerce');

        $known = ['enquiries', 'saved', 'comparisons', 'addresses', 'payment-methods', 'notifications', 'refer', 'support', 'profile'];
        abort_unless(in_array($section, $known, true), 404);

        return view('site.account.section', [
            ...$this->shared(),
            'account' => $commerce['account'],
            'section' => $section,
            'heading' => Str::headline($section),
            'addresses' => $commerce['addresses'],
            'paymentMethods' => $commerce['payment_methods'],
            'vehicles' => $this->vehicles(),
        ]);
    }

    /* ================================================================= static */

    public function search(Request $request)
    {
        $query = (string) $request->query('q', '');

        return view('site.search', [
            ...$this->shared(),
            'query' => $query,
            'vehicles' => $this->vehicles(),
            'products' => $this->liveProducts(),
            'schemes' => $this->fixture('content')['schemes'],
            'posts' => $this->fixture('content')['news'],
        ]);
    }


    public function about()
    {
        return view('site.about', $this->shared());
    }

    public function contact()
    {
        return view('site.contact', $this->shared());
    }

    public function faq()
    {
        return view('site.faq', $this->shared());
    }

    public function page(string $slug)
    {
        $titles = [
            'buying-policy' => 'AutoBazaar Vehicle Buying Policy',
            'privacy-policy' => 'Privacy Policy',
            'terms-conditions' => 'Terms & Conditions',
            'refund-policy' => 'Refund Policy',
            'shipping-policy' => 'Shipping Policy',
            'returns-refunds' => 'Returns & Refunds',
        ];

        abort_unless(isset($titles[$slug]), 404);

        return view('site.page', [
            ...$this->shared(),
            'slug' => $slug,
            'title' => $titles[$slug],
        ]);
    }
}
