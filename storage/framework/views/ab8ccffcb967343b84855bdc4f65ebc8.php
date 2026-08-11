<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['product']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['product']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="product-card">
    <div class="ph">
        <?php if($product->image_path): ?>
        <img src="<?php echo e(Storage::disk('public')->url($product->image_path)); ?>" alt="<?php echo e($product->name); ?>">
        <?php else: ?>
        <span>PHOTO 3:4</span>
        <?php endif; ?>

        <?php if($product->is_on_sale && !$product->hasVariants()): ?>
        <span class="sale-badge">Sale</span>
        <?php endif; ?>

        <button class="wishlist-btn d-none" type="button" aria-label="Add to wishlist"><i class="fa-regular fa-heart"></i></button>

        <?php if($product->hasVariants()): ?>
        <a href="<?php echo e(route('products.show', $product->slug)); ?>" class="quick-add">Choose Options</a>
        <?php elseif($product->is_in_stock): ?>
        <form action="<?php echo e(route('cart.add')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">
            <input type="hidden" name="quantity" value="1">
            <button type="submit" class="quick-add">Quick Add</button>
        </form>
        <?php else: ?>
        <span class="quick-add quick-add--disabled">Out of Stock</span>
        <?php endif; ?>
    </div>

    <a href="<?php echo e(route('products.show', $product->slug)); ?>"><h4><?php echo e($product->name); ?></h4></a>
    <div class="price">
        <?php if($product->hasVariants()): ?>
        From $<?php echo e(number_format($product->price_range['min'], 2)); ?>

        <?php elseif($product->is_on_sale): ?>
        <span class="was">$<?php echo e(number_format($product->price, 2)); ?></span>$<?php echo e(number_format($product->sale_price, 2)); ?>

        <?php else: ?>
        $<?php echo e(number_format($product->price, 2)); ?>

        <?php endif; ?>
    </div>
</div>
<?php /**PATH F:\ecommerse\design\catalog\resources\views/components/product-card.blade.php ENDPATH**/ ?>