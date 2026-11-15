<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInquiryRequest;
use App\Models\FabricProduct;
use App\Models\GalleryItem;
use App\Models\Inquiry;
use App\Models\JobOpening;
use App\Models\Post;
use App\Support\Modules;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class SiteController extends Controller
{
    public function home(): View
    {
        return view('site.home', [
            'fabrics' => $this->publishedFabrics()->limit(3)->get(),
            'posts' => $this->posts()->limit(2)->get(),
        ]);
    }

    public function about(): View
    {
        $this->guardWebsite();

        return view('site.page', [
            'title' => __('site.about.title'),
            'kicker' => __('site.about.kicker'),
            'html' => __('site.about.body'),
        ]);
    }

    public function process(): View
    {
        $this->guardWebsite();

        return view('site.process');
    }

    public function quality(): View
    {
        $this->guardWebsite();

        return view('site.page', [
            'title' => __('site.quality.title'),
            'kicker' => __('site.quality.kicker'),
            'html' => __('site.quality.body'),
        ]);
    }

    public function sustainability(): View
    {
        $this->guardWebsite();

        return view('site.page', [
            'title' => __('site.sustainability.title'),
            'kicker' => __('site.sustainability.kicker'),
            'html' => __('site.sustainability.body'),
        ]);
    }

    public function products(): View
    {
        $this->guardWebsite();

        return view('site.products', [
            'fabrics' => $this->publishedFabrics()->get(),
        ]);
    }

    public function product(FabricProduct $fabricProduct): View
    {
        $this->guardWebsite();
        abort_unless($fabricProduct->is_published, 404);

        return view('site.product', ['fabric' => $fabricProduct]);
    }

    public function gallery(): View
    {
        $this->guardWebsite();

        return view('site.gallery', [
            'items' => GalleryItem::query()->latest('id')->get(),
        ]);
    }

    public function careers(): View
    {
        $this->guardWebsite();

        return view('site.careers', [
            'jobs' => JobOpening::query()->where('is_open', true)->latest('id')->get(),
        ]);
    }

    public function career(JobOpening $jobOpening): View
    {
        $this->guardWebsite();
        abort_unless($jobOpening->is_open, 404);

        return view('site.career', ['job' => $jobOpening]);
    }

    public function blog(): View
    {
        $this->guardWebsite();

        return view('site.blog', ['posts' => $this->posts()->get()]);
    }

    public function post(Post $post): View
    {
        $this->guardWebsite();
        abort_unless($post->is_published, 404);

        return view('site.post', ['post' => $post]);
    }

    public function contact(): View
    {
        $this->guardWebsite();

        return view('site.contact', [
            'fabrics' => $this->publishedFabrics()->get(),
        ]);
    }

    public function inquire(StoreInquiryRequest $request): RedirectResponse
    {
        $this->guardWebsite();

        Inquiry::query()->create($request->validated() + ['status' => 'new']);

        return redirect()->route('contact')->with('sent', true);
    }

    public function sitemap(): Response
    {
        $urls = [route('home')];

        if (Modules::enabled('website')) {
            foreach ([route('about'), route('process'), route('quality'), route('sustainability'), route('products'), route('gallery'), route('careers'), route('blog'), route('contact')] as $url) {
                $urls[] = $url;
            }

            foreach ($this->publishedFabrics()->get() as $fabric) {
                $urls[] = route('products.show', $fabric);
            }

            foreach ($this->posts()->get() as $post) {
                $urls[] = route('blog.show', $post);
            }
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $url) {
            $xml .= '<url><loc>'.e($url).'</loc></url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    private function guardWebsite(): void
    {
        abort_unless(Modules::enabled('website'), 404);
    }

    private function publishedFabrics()
    {
        return FabricProduct::query()->where('is_published', true)->latest('id');
    }

    private function posts()
    {
        return Post::query()->where('is_published', true)->latest('published_at');
    }
}
