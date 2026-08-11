@extends('layouts.admin')

@section('title', 'Settings')
@section('page_title', 'Website Settings')

@section('main_content')
<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="card mb-3">
        <div class="card-header">General</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Site Name</label>
                    <input type="text" name="site_name" class="form-control" value="{{ old('site_name', $setting->site_name) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tagline</label>
                    <input type="text" name="site_tagline" class="form-control" value="{{ old('site_tagline', $setting->site_tagline) }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Logo</label>
                    @if ($setting->logo_path)
                    <div class="mb-2"><img src="{{ Storage::disk('public')->url($setting->logo_path) }}" style="height:48px;"></div>
                    @endif
                    <input type="file" name="logo" class="form-control" accept="image/*">
                    <div class="form-check mt-2">
                        <input type="checkbox" name="show_site_name_with_logo" value="1" class="form-check-input" id="show_site_name_with_logo"
                            {{ old('show_site_name_with_logo', $setting->show_site_name_with_logo) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="show_site_name_with_logo">
                            Show site name text next to the logo on the storefront
                        </label>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Favicon</label>
                    @if ($setting->favicon_path)
                    <div class="mb-2"><img src="{{ Storage::disk('public')->url($setting->favicon_path) }}" style="height:32px;"></div>
                    @endif
                    <input type="file" name="favicon" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Default Meta Title</label>
                    <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $setting->meta_title) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Default Meta Description</label>
                    <input type="text" name="meta_description" class="form-control" value="{{ old('meta_description', $setting->meta_description) }}">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Contact Details</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Contact Email</label>
                    <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $setting->contact_email) }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $setting->contact_phone) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">WhatsApp Number</label>
                    <input type="text" name="contact_whatsapp" class="form-control" value="{{ old('contact_whatsapp', $setting->contact_whatsapp) }}" placeholder="+64211234567">
                </div>
            </div>
            <div class="mb-0">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-control" value="{{ old('address', $setting->address) }}">
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Bank Transfer Details</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Bank Name</label>
                    <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $setting->bank_name) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Account Name</label>
                    <input type="text" name="bank_account_name" class="form-control" value="{{ old('bank_account_name', $setting->bank_account_name) }}" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Account Number</label>
                    <input type="text" name="bank_account_number" class="form-control" value="{{ old('bank_account_number', $setting->bank_account_number) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">SWIFT/BIC Code (optional)</label>
                    <input type="text" name="bank_swift_code" class="form-control" value="{{ old('bank_swift_code', $setting->bank_swift_code) }}">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Shipping (Flat Rate)</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Urban Area Rate</label>
                    <div class="input-group">
                        <span class="input-group-text">{{ $setting->currency_code }}</span>
                        <input type="number" step="0.01" min="0" name="shipping_urban_rate" class="form-control" value="{{ old('shipping_urban_rate', $setting->shipping_urban_rate) }}" required>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Rural Area Rate</label>
                    <div class="input-group">
                        <span class="input-group-text">{{ $setting->currency_code }}</span>
                        <input type="number" step="0.01" min="0" name="shipping_rural_rate" class="form-control" value="{{ old('shipping_rural_rate', $setting->shipping_rural_rate) }}" required>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Currency Code</label>
                    <input type="text" name="currency_code" class="form-control" value="{{ old('currency_code', $setting->currency_code) }}" maxlength="3" required>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Social Media</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Facebook URL</label>
                    <input type="url" name="social_facebook" class="form-control" value="{{ old('social_facebook', $setting->social_facebook) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Instagram URL</label>
                    <input type="url" name="social_instagram" class="form-control" value="{{ old('social_instagram', $setting->social_instagram) }}">
                </div>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Save Settings</button>
</form>
@endsection
