<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Contracts\View\View;

class LegalController extends PublicController
{
    public function privacy(): View
    {
        return $this->render('public.legal.privacy', [], [
            'title' => 'Privacy Policy - CDE',
            'description' => 'Privacy policy for the PT Cipta Daya Engineering website.',
            'canonical' => route('privacy'),
        ]);
    }

    public function terms(): View
    {
        return $this->render('public.legal.terms', [], [
            'title' => 'Terms of Use - CDE',
            'description' => 'Terms of use for the PT Cipta Daya Engineering website.',
            'canonical' => route('terms'),
        ]);
    }
}
