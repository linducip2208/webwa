<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProgrammaticSeoController extends Controller
{
    public function city(string $city): View
    {
        abort_unless(in_array($city, config('webwa.cities'), true), 404);

        return view('pseo.city', [
            'city' => $city,
            'cityName' => Str::title(str_replace('-', ' ', $city)),
        ]);
    }

    public function industry(string $industry): View
    {
        abort_unless(in_array($industry, config('webwa.industries'), true), 404);

        return view('pseo.industry', [
            'industry' => $industry,
            'industryName' => Str::title(str_replace('-', ' ', $industry)),
        ]);
    }

    public function best(): View
    {
        return view('pseo.best', ['year' => config('webwa.year')]);
    }

    public function alternative(string $competitor): View
    {
        abort_unless(in_array($competitor, config('webwa.competitors'), true), 404);

        return view('pseo.alternative', [
            'competitor' => $competitor,
            'competitorName' => Str::title($competitor),
        ]);
    }

    public function compare(string $a, string $b): View
    {
        $competitors = config('webwa.competitors');
        abort_unless(in_array($a, $competitors, true) && in_array($b, $competitors, true), 404);

        return view('pseo.compare', [
            'a' => $a, 'b' => $b,
            'aName' => Str::title($a), 'bName' => Str::title($b),
        ]);
    }
}
