<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use App\Models\Shop\Address;
use App\Models\Shop\Order;
use App\Models\Vehicle\VehicleEnquiry;
use App\Models\spareparts\Product;
use App\Models\spareparts\SparepartsSubCategories;
use App\Services\CartService;
use App\Services\VehicleCatalogService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Customer-facing AutoBazaar website.
 *
 * Vehicles come from the catalogue tables via VehicleCatalogService and the
 * accessories shop from the live product tables. Offers, schemes, news and
 * account sections still read resources/fixtures until their admin modules
 * exist.
 */
class SiteController extends Controller
{
    public function __construct(private VehicleCatalogService $catalog)
    {
    }

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
            'site' => \App\Support\SiteData::site(),
            'locations' => $this->fixture('locations'),
        ];
    }

    /** Live catalogue cards (fixture-shaped), memoised per request. */
    private function vehicles(): array
    {
        return $this->cache['__vehicles'] ??= $this->catalog->all();
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

    public function usedAutos(\App\Services\UsedAutoService $used)
    {
        // Real listings: the same active used autos the mobile app and admin show.
        return view('site.vehicles.used', [
            ...$this->shared(),
            'title' => 'Used Autos',
            'lede' => 'Pre-owned autorickshaws with RC, FC and permit status shown up front.',
            'listings' => $used->all(),
        ]);
    }

    public function usedAuto(\App\Services\UsedAutoService $used, int $id, ?string $slug = null)
    {
        $listing = $used->find($id);

        // Sold, removed or never existed: a clean 404 rather than a half-empty page.
        abort_if(! $listing, 404);

        // One address per auto, so a renamed model does not create duplicate pages for Google.
        if ($slug !== $listing['slug']) {
            return redirect()->route('site.used-auto', [$listing['id'], $listing['slug']], 301);
        }

        return view('site.vehicles.used-show', [
            ...$this->shared(),
            'listing' => $listing,
            'similar' => $used->similar($listing, 3),
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
        $record = $this->catalog->findByBrandModel($brand, $model);

        abort_if(! $record, 404);

        return view('site.vehicles.show', [
            ...$this->shared(),
            'vehicle' => $this->catalog->detail($record),
            'similar' => $this->catalog->similar($record, 4),
        ]);
    }

    /* ================================================================= compare */

    public function compare(?string $combo = null)
    {
        $all = $this->vehicles();

        // /compare/tvs-king-deluxe-vs-bajaj-re -> the named models. With no selection the
        // page starts empty and asks the visitor to pick (it used to preload four, which
        // made "remove" look like it was undoing itself).
        $selected = $combo
            ? array_values(array_filter(array_map(
                fn ($slug) => $this->vehicle($slug),
                explode('-vs-', $combo),
            )))
            : [];   // the visitor chooses; the page invites them to pick 2 to 4

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
            // A server whose vehicle catalogue has not been filled in yet has no
            // vehicles at all, and reaching straight for [0] took this page down
            // with a server error. The enquiry form is still useful without one.
            'vehicle' => ($model ? $this->vehicle($model) : null) ?? $this->vehicles()[0] ?? $this->placeholderVehicle(),
            'vehicles' => $this->vehicles(),
        ]);
    }

    /**
     * Stands in for a real auto when the catalogue is empty, carrying only the
     * keys the enquiry page reads.
     */
    private function placeholderVehicle(): array
    {
        return [
            'name'       => 'Autorickshaw',
            'brand'      => '',
            'brand_slug' => '',
            'model_slug' => '',
            'slug'       => '',
            'image'      => 'assets/site/no-image.png',
            'brand_logo' => 'assets/site/no-image.png',
            'from_price' => null,
            'rating'     => null,
            'reviews'    => 0,
            'variants'   => [],
        ];
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

    /**
     * Account pages show the signed-in customer's own data (routes sit behind
     * UserAuth). `account` still carries the sidebar menu from the fixture.
     */
    private function accountChrome(): array
    {
        return ['account' => $this->fixture('commerce')['account']];
    }

    /** Leads this customer sent, matched on their login or their mobile number. */
    private function customerEnquiries()
    {
        $user = Auth::user();

        return VehicleEnquiry::with('model')
            ->where('status_id', 1)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id);
                if ($user->phone_number) {
                    $q->orWhere('mobile', $user->phone_number);
                }
            })
            ->latest('id');
    }

    public function account(CartService $cart)
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)->where('status_id', 1);

        return view('site.account.dashboard', [
            ...$this->shared(),
            ...$this->accountChrome(),
            'firstName' => Str::of($user->name ?: 'there')->before(' ')->toString(),
            'stats' => [
                'orders' => (clone $orders)->count(),
                'enquiries' => $this->customerEnquiries()->count(),
                'addresses' => Address::where('user_id', $user->id)->where('status_id', 1)->count(),
                'cart' => $cart->itemCount(),
            ],
            'latestOrder' => (clone $orders)->with('items')->latest('id')->first(),
        ]);
    }

    public function orders()
    {
        return view('site.account.orders', [
            ...$this->shared(),
            ...$this->accountChrome(),
            'orders' => Order::where('user_id', Auth::id())->where('status_id', 1)
                ->with('items')->latest('id')->get(),
        ]);
    }

    /** One order — always scoped to the signed-in customer. */
    public function order(string $id)
    {
        $order = Order::where('order_number', $id)
            ->where('user_id', Auth::id())
            ->where('status_id', 1)
            ->with(['items', 'history'])
            ->firstOrFail();

        return view('site.order-confirmed', [
            ...$this->shared(),
            'order' => $order,
        ]);
    }

    public function accountSection(string $section)
    {
        $known = ['enquiries', 'saved', 'comparisons', 'addresses', 'payment-methods', 'notifications', 'refer', 'support', 'profile'];
        abort_unless(in_array($section, $known, true), 404);

        $user = Auth::user();

        $addresses = Address::where('user_id', $user->id)->where('status_id', 1)
            ->orderByDesc('is_default')->orderBy('id')->get()
            ->map(fn (Address $a) => [
                'label' => $a->label,
                'is_default' => (bool) $a->is_default,
                'name' => $a->name,
                'lines' => $a->lines,
                'phone' => $a->mobile,
            ])->all();

        /*
         * Order updates, newest first. Opening the page marks them read — the
         * view reads which were unread before this runs, so the blue dots are
         * still shown on the visit that clears them.
         */
        $notifications = collect();

        if ($section === 'notifications') {
            $notifications = $user->notifications()->latest()->limit(50)->get();
            $user->unreadNotifications->markAsRead();
        }

        return view('site.account.section', [
            ...$this->shared(),
            ...$this->accountChrome(),
            'notifications' => $notifications,
            'section' => $section,
            'heading' => Str::headline($section),
            'addresses' => $addresses,
            'enquiries' => $section === 'enquiries' ? $this->customerEnquiries()->limit(50)->get() : collect(),
            'customer' => $user,
            'paymentMethods' => [],
            'vehicles' => $this->vehicles(),
        ]);
    }

    /* ================================================================= static */

    public function search(Request $request)
    {
        $q = $request->query('q');
        $query = is_string($q) ? trim($q) : '';   // ?q[]=x must not crash the page
        $query = mb_substr($query, 0, 100);

        // Every word must appear somewhere in the item's searchable text.
        $words = array_values(array_filter(preg_split('/\s+/', mb_strtolower($query))));
        $matches = function (array $haystack) use ($words): bool {
            $text = mb_strtolower(implode(' ', array_filter($haystack, 'is_scalar')));
            foreach ($words as $word) {
                if (! str_contains($text, $word)) {
                    return false;
                }
            }

            return true;
        };
        $filter = fn (array $items, callable $fields) => $words
            ? array_values(array_filter($items, fn ($item) => $matches($fields($item))))
            : $items;

        $content = $this->fixture('content');

        return view('site.search', [
            ...$this->shared(),
            'query' => $query,
            'vehicles' => $filter($this->vehicles(), fn ($v) => [
                $v['name'], $v['brand'], $v['tagline'] ?? '', $v['description'] ?? '',
                implode(' ', array_column($v['variants'] ?? [], 'label')),
            ]),
            'products' => $filter($this->liveProducts(), fn ($p) => [
                $p['name'], $p['subtitle'] ?? '', $p['category'] ?? '', $p['brand'] ?? '',
            ]),
            'schemes' => $filter($content['schemes'], fn ($s) => [
                $s['title'], $s['authority'] ?? '', $s['strap'] ?? '', implode(' ', $s['bullets'] ?? []),
            ]),
            'posts' => $filter($content['news'], fn ($n) => [
                $n['title'], $n['category'] ?? '', $n['excerpt'] ?? '',
            ]),
        ]);
    }

    public function login()
    {
        return view('site.login', $this->shared());
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
