<?php

namespace App\Http\Controllers\Web\Auth;

use Illuminate\Http\Request;
use App\Models\Masters\AutoAreas;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Auth\Concerns\RendersAuthPage;

class RegisterController extends Controller
{
    use RendersAuthPage;

    public function index()
    {
        // Areas are loaded per district on demand (see areas()); printing all
        // 16,000+ options made this a 1.6 MB page.
        $cities = \Illuminate\Support\Facades\DB::table('tbl_auto_cities')->orderBy('id')->get(['id', 'name']);
        $areas = [];

        return $this->authView('register', compact('areas', 'cities'));
    }

    /** GET user/areas/{city} — area options for one district (0 = every other district). */
    public function areas($city)
    {
        $known = \Illuminate\Support\Facades\DB::table('tbl_auto_cities')->pluck('id')->all();

        $areas = AutoAreas::where('status', 1)
            ->when((int) $city > 0, fn ($q) => $q->where('city_id', (int) $city), fn ($q) => $q->whereNotIn('city_id', $known))
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['success' => true, 'areas' => $areas]);
    }
}
