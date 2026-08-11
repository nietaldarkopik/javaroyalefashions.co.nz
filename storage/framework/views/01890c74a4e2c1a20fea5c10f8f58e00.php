<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['setting']));

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

foreach (array_filter((['setting']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<a href="<?php echo e(route('home')); ?>" <?php echo e($attributes->merge(['class' => 'logo'])); ?>>
    <?php if($setting->logo_path): ?>
    <img src="<?php echo e(Storage::disk('public')->url($setting->logo_path)); ?>" alt="<?php echo e($setting->site_name); ?>">
    <?php if($setting->show_site_name_with_logo): ?>
    <span><?php echo e($setting->site_name); ?></span>
    <?php endif; ?>
    <?php else: ?>
    <?php echo e($setting->site_name); ?>

    <?php endif; ?>
</a>
<?php /**PATH F:\ecommerse\design\catalog\resources\views/components/site-logo.blade.php ENDPATH**/ ?>