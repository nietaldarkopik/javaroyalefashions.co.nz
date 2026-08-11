<?php $__env->startSection('title', $siteSetting->site_name.' — '.$siteSetting->site_tagline); ?>

<?php $__env->startSection('content'); ?>

<section class="hero">
  <div class="hero-slide active">
    <div class="ph"><span>HERO PHOTO — 1600×900</span></div>
    <div class="hero-content">
      <span class="eyebrow"><?php echo e($siteSetting->site_tagline); ?></span>
      <h1>Quality goods,<br>shipped across NZ.</h1>
      <p>Browse the catalog, add what you need to your cart, and check out as a guest — no account required.</p>
      <a href="<?php echo e(route('products.index')); ?>" class="btn">Shop All Products</a>
    </div>
  </div>
  <div class="hero-dots"></div>
</section>

<div class="stitch"></div>

<?php if($categories->isNotEmpty()): ?>
<section class="section reveal" id="kategori">
  <div class="wrap">
    <div class="section-head">
      <div>
        <span class="eyebrow">Shop By</span>
        <h2>Category</h2>
      </div>
      <div class="carousel-arrows">
        <button class="carousel-arrow" data-carousel-prev aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>
        <button class="carousel-arrow" data-carousel-next aria-label="Next"><i class="fa-solid fa-chevron-right"></i></button>
      </div>
    </div>
    <div class="carousel-wrap">
      <div class="cat-grid carousel-track">
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a class="cat-card" href="<?php echo e(route('categories.show', $category->slug)); ?>" style="width:170px;">
          <div class="ph">
            <?php if($category->image_path): ?>
            <img src="<?php echo e(Storage::disk('public')->url($category->image_path)); ?>" alt="<?php echo e($category->name); ?>">
            <?php else: ?>
            <span><?php echo e(strtoupper($category->name)); ?></span>
            <?php endif; ?>
          </div>
          <h3><?php echo e($category->name); ?></h3>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>
  </div>
</section>

<div class="stitch stitch--rust"></div>
<?php endif; ?>

<?php if($featuredProducts->isNotEmpty()): ?>
<section class="section reveal" id="featured">
  <div class="wrap">
    <div class="section-head">
      <div>
        <span class="eyebrow">Featured</span>
        <h2>Popular Right Now</h2>
      </div>
      <a href="<?php echo e(route('products.index')); ?>" class="view-all">View All <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="product-grid">
      <?php $__currentLoopData = $featuredProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php if (isset($component)) { $__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-card','data' => ['product' => $product]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a)): ?>
<?php $attributes = $__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a; ?>
<?php unset($__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a)): ?>
<?php $component = $__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a; ?>
<?php unset($__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a); ?>
<?php endif; ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="video-banner reveal">
  <div class="ph"><span>BANNER PHOTO — 1920×1080</span></div>
  <div class="video-banner-content">
    <span class="eyebrow">Our Story</span>
    <h2>Quality You Can Trust</h2>
    <p>Learn more about who we are, how we work, and why customers keep coming back.</p>
    <a href="<?php echo e(route('pages.show', 'about')); ?>" class="btn">Read Our Story</a>
  </div>
</section>

<section class="badges reveal">
  <div class="wrap badge-grid">
    <div class="badge-item"><div class="icon"><i class="fa-solid fa-star"></i></div><p>Carefully selected, quality products</p></div>
    <div class="badge-item"><div class="icon"><i class="fa-solid fa-truck"></i></div><p>Flat-rate shipping, urban &amp; rural NZ</p></div>
    <div class="badge-item"><div class="icon"><i class="fa-solid fa-shield-halved"></i></div><p>Secure checkout, no account needed</p></div>
    <div class="badge-item"><div class="icon"><i class="fa-solid fa-building-columns"></i></div><p>Simple manual bank transfer payment</p></div>
  </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.front', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\ecommerse\design\catalog\resources\views/front/home/index.blade.php ENDPATH**/ ?>