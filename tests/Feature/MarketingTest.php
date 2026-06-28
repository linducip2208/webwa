<?php

namespace Tests\Feature;

use App\Http\Middleware\RequirePair;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(RequirePair::class);
    }

    public function test_landing_page_loads(): void
    {
        $this->get('/')->assertOk()->assertSee('WebWA', false);
    }

    public function test_docs_pricing_blog_load(): void
    {
        $this->get('/docs')->assertOk();
        $this->get('/harga')->assertOk();
        $this->get('/blog')->assertOk();
    }

    public function test_sitemap_contains_urlset(): void
    {
        $this->get('/sitemap.xml')->assertOk()->assertSee('urlset', false);
    }

    public function test_pseo_city_page_loads(): void
    {
        $this->get('/whatsapp-gateway-jakarta')->assertOk()->assertSee('Jakarta', false);
    }

    public function test_invalid_pseo_city_returns_404(): void
    {
        $this->get('/whatsapp-gateway-kotantah')->assertNotFound();
    }

    public function test_published_blog_post_loads(): void
    {
        $cat = BlogCategory::create(['name' => 'Tips', 'slug' => 'tips']);
        BlogPost::create([
            'category_id' => $cat->id,
            'title' => 'Halo Dunia',
            'slug' => 'halo-dunia',
            'excerpt' => 'ringkas',
            'content' => '<p>isi artikel</p>',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        $this->get('/blog/halo-dunia')->assertOk()->assertSee('Halo Dunia', false);
    }
}
