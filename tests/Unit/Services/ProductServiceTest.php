<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTO\ProductDTO;
use App\Events\ProductCreated;
use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        Cache::shouldReceive('remember')
            ->andReturnUsing(function ($key, $ttl, $callback) {
                return $callback();
            });
            
        Cache::shouldReceive('forget')
            ->andReturn(true);
            
        Event::shouldReceive('dispatch')
            ->andReturn(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_products_returns_collection(): void
    {
        $products = new Collection([
            new Product(['name' => 'Test Product', 'price' => 10.00, 'stock' => 5]),
        ]);

        $repository = Mockery::mock(ProductRepositoryInterface::class);
        $repository->shouldReceive('all')->once()->andReturn($products);

        $service = new ProductService($repository);
        $result = $service->getAllProducts();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(1, $result->count());
        $this->assertEquals('Test Product', $result->first()->name);
    }

    public function test_get_product_by_id_returns_product(): void
    {
        $product = new Product(['name' => 'Test Product', 'price' => 10.00, 'stock' => 5]);

        $repository = Mockery::mock(ProductRepositoryInterface::class);
        $repository->shouldReceive('find')->with(1)->once()->andReturn($product);

        $service = new ProductService($repository);
        $result = $service->getProduct(1);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals('Test Product', $result->name);
    }

    public function test_create_product_returns_new_product(): void
    {
        $productDTO = new ProductDTO(
            name: 'New Product',
            price: 15.00,
            stock: 10
        );

        $product = new Product([
            'name' => $productDTO->name,
            'price' => $productDTO->price,
            'stock' => $productDTO->stock,
        ]);

        $repository = Mockery::mock(ProductRepositoryInterface::class);
        $repository->shouldReceive('create')->once()->andReturn($product);

        $service = new ProductService($repository);
        $result = $service->createProduct($productDTO);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals('New Product', $result->name);
        $this->assertEquals(15.00, $result->price);
        $this->assertEquals(10, $result->stock);
    }

    public function test_is_in_stock_returns_true_when_product_has_stock(): void
    {
        $product = new Product(['name' => 'Test Product', 'price' => 10.00, 'stock' => 5]);

        $repository = Mockery::mock(ProductRepositoryInterface::class);
        $repository->shouldReceive('find')->with(1)->once()->andReturn($product);

        $service = new ProductService($repository);
        $result = $service->isInStock(1);

        $this->assertTrue($result);
    }

    public function test_is_in_stock_returns_false_when_product_has_no_stock(): void
    {
        $product = new Product(['name' => 'Test Product', 'price' => 10.00, 'stock' => 0]);

        $repository = Mockery::mock(ProductRepositoryInterface::class);
        $repository->shouldReceive('find')->with(1)->once()->andReturn($product);

        $service = new ProductService($repository);
        $result = $service->isInStock(1);

        $this->assertFalse($result);
    }
} 