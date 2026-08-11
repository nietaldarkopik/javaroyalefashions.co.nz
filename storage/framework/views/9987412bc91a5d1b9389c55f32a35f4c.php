<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $__env->yieldContent('title', $siteSetting->site_name.' — '.$siteSetting->site_tagline); ?></title>
<meta name="description" content="<?php echo $__env->yieldContent('meta_description', $siteSetting->meta_description); ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="<?php echo e(asset('css/styles.css')); ?>">
<?php if($siteSetting->favicon_path): ?>
<link rel="icon" href="<?php echo e(Storage::disk('public')->url($siteSetting->favicon_path)); ?>">
<?php endif; ?>
</head>
<body>

<div class="promo-popup-backdrop"></div>
<div class="promo-popup">
  <button class="promo-popup-close" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
  <div class="ph"><span>PROMO PHOTO — 800×500</span></div>
  <div class="promo-popup-body">
    <span class="eyebrow">New Here?</span>
    <h3>Get 15% Off<br>Your First Order</h3>
    <p>Sign up now for an exclusive discount code on your first purchase.</p>
    <form class="promo-popup-form">
      <input type="email" placeholder="Your email address" required>
      <button type="submit">Claim</button>
    </form>
    <a href="#" class="promo-popup-skip" data-promo-skip>Maybe later</a>
  </div>
</div>

<div class="announce">
  FLAT RATE SHIPPING &nbsp;·&nbsp; URBAN $<?php echo e(number_format($siteSetting->shipping_urban_rate, 2)); ?> &nbsp;·&nbsp; RURAL $<?php echo e(number_format($siteSetting->shipping_rural_rate, 2)); ?>

</div>

<header class="site">
  <div class="wrap header-inner px-5">
    <button class="menu-toggle" aria-label="Open menu"><i class="fa-solid fa-bars"></i></button>
    <?php if (isset($component)) { $__componentOriginal0e3e854f1972cb532cc8b5bc0ace80b0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0e3e854f1972cb532cc8b5bc0ace80b0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.site-logo','data' => ['setting' => $siteSetting]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('site-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['setting' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($siteSetting)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0e3e854f1972cb532cc8b5bc0ace80b0)): ?>
<?php $attributes = $__attributesOriginal0e3e854f1972cb532cc8b5bc0ace80b0; ?>
<?php unset($__attributesOriginal0e3e854f1972cb532cc8b5bc0ace80b0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0e3e854f1972cb532cc8b5bc0ace80b0)): ?>
<?php $component = $__componentOriginal0e3e854f1972cb532cc8b5bc0ace80b0; ?>
<?php unset($__componentOriginal0e3e854f1972cb532cc8b5bc0ace80b0); ?>
<?php endif; ?>
    <nav class="main-nav">
      <a href="<?php echo e(route('products.index')); ?>">All Products</a>
      <div class="nav-item">
        <button class="nav-link">Categories <span class="caret"><i class="fa-solid fa-chevron-down"></i></span></button>
        <div class="dropdown">
          <?php $__empty_1 = true; $__currentLoopData = $navCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $navCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <a href="<?php echo e(route('categories.show', $navCategory->slug)); ?>"><?php echo e($navCategory->name); ?></a>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <a href="<?php echo e(route('products.index')); ?>">All Products</a>
          <?php endif; ?>
        </div>
      </div>
      <a href="<?php echo e(route('pages.show', 'about')); ?>">About</a>
      <a href="<?php echo e(route('pages.show', 'contact')); ?>">Contact</a>
    </nav>
    <div class="header-icons">
      <button data-search-open aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
      <button class="icon-btn" data-cart-open aria-label="Cart"><i class="fa-solid fa-cart-shopping"></i><span class="cart-count"><?php echo e($cartCount); ?></span></button>
    </div>
  </div>

  <div class="search-overlay">
    <div class="search-inner">
      <form action="<?php echo e(route('products.index')); ?>" method="GET" style="flex:1; display:flex;">
        <input type="text" name="search" placeholder="Search products…" value="<?php echo e(request('search')); ?>">
      </form>
      <button class="search-close" aria-label="Close search"><i class="fa-solid fa-xmark"></i></button>
    </div>
  </div>
</header>

<div class="mobile-nav-backdrop"></div>
<div class="mobile-nav">
  <div class="mobile-nav-head">
    <?php if (isset($component)) { $__componentOriginal0e3e854f1972cb532cc8b5bc0ace80b0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0e3e854f1972cb532cc8b5bc0ace80b0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.site-logo','data' => ['setting' => $siteSetting]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('site-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['setting' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($siteSetting)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0e3e854f1972cb532cc8b5bc0ace80b0)): ?>
<?php $attributes = $__attributesOriginal0e3e854f1972cb532cc8b5bc0ace80b0; ?>
<?php unset($__attributesOriginal0e3e854f1972cb532cc8b5bc0ace80b0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0e3e854f1972cb532cc8b5bc0ace80b0)): ?>
<?php $component = $__componentOriginal0e3e854f1972cb532cc8b5bc0ace80b0; ?>
<?php unset($__componentOriginal0e3e854f1972cb532cc8b5bc0ace80b0); ?>
<?php endif; ?>
    <button class="mobile-nav-close" aria-label="Close menu"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <a href="<?php echo e(route('products.index')); ?>">All Products</a>
  <div class="m-nav-item">
    <button class="m-nav-link">Categories <span class="caret"><i class="fa-solid fa-chevron-down"></i></span></button>
    <div class="m-submenu">
      <?php $__currentLoopData = $navCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $navCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <a href="<?php echo e(route('categories.show', $navCategory->slug)); ?>"><?php echo e($navCategory->name); ?></a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
  <a href="<?php echo e(route('pages.show', 'about')); ?>">About</a>
  <a href="<?php echo e(route('pages.show', 'contact')); ?>">Contact</a>
</div>

<div class="drawer-backdrop"></div>
<div class="cart-drawer">
  <div class="cart-head">
    <h3>Your Cart</h3>
    <button class="cart-close" aria-label="Close cart"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <div class="cart-body">
    <?php $__empty_1 = true; $__currentLoopData = $miniCartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="cart-line">
      <div class="ph">
        <?php if($line['variant']?->image_path ?? $line['product']->image_path): ?>
        <img src="<?php echo e(Storage::disk('public')->url($line['variant']->image_path ?? $line['product']->image_path)); ?>" alt="<?php echo e($line['product']->name); ?>">
        <?php else: ?>
        <span>PHOTO</span>
        <?php endif; ?>
      </div>
      <div class="cart-line-info">
        <h5><?php echo e($line['product']->name); ?></h5>
        <?php if($line['variant']): ?>
        <div class="meta"><?php echo e($line['variant']->label); ?></div>
        <?php endif; ?>
        <div class="meta">Qty <?php echo e($line['quantity']); ?></div>
        <div class="price">$<?php echo e(number_format($line['line_total'], 2)); ?></div>
        <form action="<?php echo e(route('cart.remove', $line['line_key'])); ?>" method="POST">
          <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
          <button type="submit" class="cart-remove">Remove</button>
        </form>
      </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="cart-empty">
      <div class="icon"><i class="fa-solid fa-cart-shopping"></i></div>
      <p>Your cart is empty.</p>
    </div>
    <?php endif; ?>
  </div>
  <div class="cart-foot">
    <div class="cart-subtotal"><span>Subtotal</span><span>$<?php echo e(number_format($miniCartSubtotal, 2)); ?></span></div>
    <p class="cart-note">Shipping is calculated at checkout.</p>
    <a href="<?php echo e($miniCartItems->isEmpty() ? route('cart.index') : route('checkout.index')); ?>" class="btn btn--rust" style="width:100%; justify-content:center;">
      <?php echo e($miniCartItems->isEmpty() ? 'View Cart' : 'Checkout'); ?>

    </a>
  </div>
</div>

<?php if(session('status')): ?>
<div class="wrap" style="padding-top:16px;">
  <div class="chip" style="background:var(--ink); color:var(--paper); cursor:default;"><?php echo e(session('status')); ?></div>
</div>
<?php endif; ?>

<?php echo $__env->yieldContent('content'); ?>

<section class="newsletter">
  <div class="wrap">
    <span class="eyebrow">Stay Connected</span>
    <h2>Be the first to know about new arrivals</h2>
    <p>Sign up for exclusive offers, early access, and the stories behind what we sell.</p>
    <form class="news-form">
      <input type="email" placeholder="Your email address" required>
      <button type="submit">Subscribe</button>
    </form>
  </div>
</section>

<footer class="site">
  <div class="wrap">
    <div class="footer-grid">
      <div>
        <?php if (isset($component)) { $__componentOriginal0e3e854f1972cb532cc8b5bc0ace80b0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0e3e854f1972cb532cc8b5bc0ace80b0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.site-logo','data' => ['setting' => $siteSetting,'class' => 'footer-logo']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('site-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['setting' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($siteSetting),'class' => 'footer-logo']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0e3e854f1972cb532cc8b5bc0ace80b0)): ?>
<?php $attributes = $__attributesOriginal0e3e854f1972cb532cc8b5bc0ace80b0; ?>
<?php unset($__attributesOriginal0e3e854f1972cb532cc8b5bc0ace80b0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0e3e854f1972cb532cc8b5bc0ace80b0)): ?>
<?php $component = $__componentOriginal0e3e854f1972cb532cc8b5bc0ace80b0; ?>
<?php unset($__componentOriginal0e3e854f1972cb532cc8b5bc0ace80b0); ?>
<?php endif; ?>
        <p><?php echo e($siteSetting->site_tagline); ?></p>
      </div>
      <div>
        <h5>Shop</h5>
        <ul>
          <li><a href="<?php echo e(route('products.index')); ?>">All Products</a></li>
          <?php $__currentLoopData = $navCategories->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $navCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li><a href="<?php echo e(route('categories.show', $navCategory->slug)); ?>"><?php echo e($navCategory->name); ?></a></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
      <div>
        <h5>Help</h5>
        <ul>
          <li><a href="<?php echo e(route('pages.show', 'about')); ?>">About Us</a></li>
          <li><a href="<?php echo e(route('pages.show', 'contact')); ?>">Contact Us</a></li>
          <li><a href="<?php echo e(route('pages.show', 'privacy-policy')); ?>">Privacy Policy</a></li>
          <li><a href="<?php echo e(route('pages.show', 'terms')); ?>">Terms &amp; Conditions</a></li>
        </ul>
      </div>
      <div>
        <h5>Get in Touch</h5>
        <ul>
          <?php if($siteSetting->address): ?><li><?php echo e($siteSetting->address); ?></li><?php endif; ?>
          <li><a href="mailto:<?php echo e($siteSetting->contact_email); ?>"><?php echo e($siteSetting->contact_email); ?></a></li>
          <?php if($siteSetting->contact_phone): ?><li><?php echo e($siteSetting->contact_phone); ?></li><?php endif; ?>
          <?php if($siteSetting->contact_whatsapp): ?>
          <li><a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $siteSetting->contact_whatsapp)); ?>" target="_blank">WhatsApp Us</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>&copy; <?php echo e(date('Y')); ?> <?php echo e($siteSetting->site_name); ?>. All rights reserved.</span>
      <span>Manual bank transfer only &middot; Guest checkout, no account needed.</span>
    </div>
  </div>
</footer>

<script src="<?php echo e(asset('js/app.js')); ?>"></script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH F:\ecommerse\design\catalog\resources\views/layouts/front.blade.php ENDPATH**/ ?>