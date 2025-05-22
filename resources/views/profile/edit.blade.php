@extends('layouts.sneat')

@section('title', 'Profile Settings')

@push('styles')
<style>
.profile-header {
    background: linear-gradient(135deg, #696cff 0%, #9155fd 100%);
    border-radius: 0.5rem;
    color: white;
    position: relative;
    overflow: hidden;
}

.profile-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    opacity: 0.5;
}

.avatar-wrapper {
    position: relative;
    display: inline-block;
}

.avatar-upload {
    position: absolute;
    bottom: 0;
    right: 0;
    background: #696cff;
    border: 3px solid white;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.avatar-upload:hover {
    background: #5a67d8;
    transform: scale(1.1);
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(161, 172, 184, 0.4);
    border: none;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(161, 172, 184, 0.3);
}

.section-title {
    color: #566a7f;
    font-weight: 600;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.section-title i {
    margin-right: 0.5rem;
    color: #696cff;
}
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Profile Header -->
    <div class="row">
        <div class="col-12">
            <div class="card profile-header mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="avatar-wrapper">
                                <div class="avatar avatar-xl">
                                    <img src="{{ $user->avatar_url }}" alt="Avatar" class="rounded-circle" id="currentAvatar" style="width: 80px; height: 80px; object-fit: cover;">
                                </div>
                                <div class="avatar-upload" onclick="$('#avatarInput').click()">
                                    <i class="bx bx-camera text-white" style="font-size: 14px;"></i>
                                </div>
                                <input type="file" id="avatarInput" accept="image/*" style="display: none;">
                            </div>
                        </div>
                        <div class="col">
                            <h3 class="mb-1 text-white">{{ $user->name }}</h3>
                            <p class="mb-0 text-white-50">
                                <i class="bx bx-envelope me-2"></i>{{ $user->email }}
                            </p>
                            <p class="mb-0 text-white-50">
                                <i class="bx bx-shield me-2"></i>{{ $user->role->display_name ?? 'User' }}
                            </p>
                        </div>
                        <div class="col-auto">
                            <div class="text-end">
                                <div class="badge bg-light text-dark mb-2">
                                    Member since {{ $user->created_at->format('M Y') }}
                                </div>
                                <br>
                                <small class="text-white-50">
                                    Last updated {{ $user->updated_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Profile Information -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="section-title mb-0">
                        <i class="bx bx-user"></i>
                        Profile Information
                    </h5>
                </div>
                <div class="card-body">
                    <form id="profileForm">
                        @csrf
                        @method('patch')
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                            <div class="invalid-feedback"></div>
                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="alert alert-warning mt-2">
                                    <small>
                                        Your email address is unverified.
                                        <button type="button" class="btn btn-link p-0 text-decoration-underline" onclick="resendVerification()">
                                            Click here to re-send the verification email.
                                        </button>
                                    </small>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between">
                            <div></div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="section-title mb-0">
                        <i class="bx bx-lock"></i>
                        Change Password
                    </h5>
                </div>
                <div class="card-body">
                    <form id="passwordForm">
                        @csrf
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                    <i class="bx bx-show" id="current_password_icon"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                    <i class="bx bx-show" id="password_icon"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback"></div>
                            <small class="text-muted">Minimum 8 characters</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                    <i class="bx bx-show" id="password_confirmation_icon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <div></div>
                            <button type="submit" class="btn btn-warning">
                                <i class="bx bx-key me-1"></i>
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Information & Danger Zone -->
    <div class="row">
        <!-- Account Information -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="section-title mb-0">
                        <i class="bx bx-info-circle"></i>
                        Account Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <p class="mb-2"><strong>User ID:</strong></p>
                            <p class="text-muted">#{{ $user->id }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-2"><strong>Role:</strong></p>
                            <span class="badge bg-primary">{{ $user->role->display_name ?? 'User' }}</span>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-6">
                            <p class="mb-2"><strong>Account Created:</strong></p>
                            <p class="text-muted">{{ $user->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-2"><strong>Last Login:</strong></p>
                            <p class="text-muted">{{ $user->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    @if($user->role && $user->role->permissions->count() > 0)
                    <div class="mt-3">
                        <p class="mb-2"><strong>Permissions:</strong></p>
                        <div>
                            @foreach($user->role->permissions as $permission)
                                <span class="badge bg-label-primary me-1 mb-1">{{ $permission->display_name }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="col-md-6">
            <div class="card border-danger mb-4">
                <div class="card-header border-danger">
                    <h5 class="section-title mb-0 text-danger">
                        <i class="bx bx-error-circle"></i>
                        Danger Zone
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Once you delete your account, all of its resources and data will be permanently deleted. 
                        Before deleting your account, please download any data or information that you wish to retain.
                    </p>
                    
                    <button type="button" class="btn btn-outline-danger" onclick="confirmDeleteAccount()">
                        <i class="bx bx-trash me-1"></i>
                        Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-danger">
                <h5 class="modal-title text-danger">
                    <i class="bx bx-error-circle me-2"></i>
                    Delete Account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteAccountForm">
                @csrf
                @method('delete')
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <strong>Warning!</strong> This action cannot be undone. This will permanently delete your account and all associated data.
                    </div>
                    
                    <div class="mb-3">
                        <label for="delete_password" class="form-label">Enter your password to confirm:</label>
                        <input type="password" class="form-control" id="delete_password" name="password" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bx bx-trash me-1"></i>
                        Delete My Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // CSRF Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Profile Form Submit
    $('#profileForm').submit(function(e) {
        e.preventDefault();
        console.log('Profile form submitted'); // Debug log
        
        var formData = $(this).serialize();
        
        // Clear errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        $.ajax({
            url: "{{ route('profile.update') }}",
            type: 'POST', // Changed to POST
            data: formData + '&_method=PATCH',
            beforeSend: function() {
                $('#profileForm button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Updating...');
            },
            success: function(response) {
                console.log('Profile update response:', response); // Debug log
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    
                    // Update header info if data is provided
                    if (response.data) {
                        $('.profile-header h3').text(response.data.name);
                        $('.profile-header p:contains("@")').html('<i class="bx bx-envelope me-2"></i>' + response.data.email);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Something went wrong!'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log('Profile update error:', xhr, status, error); // Debug log
                
                var response = xhr.responseJSON;
                
                if (xhr.status === 422 && response && response.errors) {
                    // Validation errors
                    $.each(response.errors, function(field, messages) {
                        var input = $('#' + field);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(messages[0]);
                    });
                    
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please check the form fields and try again.'
                    });
                } else {
                    // Other errors
                    var errorMessage = response && response.message ? response.message : 'Something went wrong!';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage
                    });
                }
            },
            complete: function() {
                $('#profileForm button[type="submit"]').prop('disabled', false).html('<i class="bx bx-save me-1"></i>Update Profile');
            }
        });
    });

    // Password Form Submit
    $('#passwordForm').submit(function(e) {
        e.preventDefault();
        console.log('Password form submitted'); // Debug log
        
        var formData = $(this).serialize();
        
        // Clear errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        $.ajax({
            url: "{{ route('profile.update-password') }}",
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#passwordForm button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Updating...');
            },
            success: function(response) {
                console.log('Password update response:', response); // Debug log
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    $('#passwordForm')[0].reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Something went wrong!'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log('Password update error:', xhr, status, error); // Debug log
                
                var response = xhr.responseJSON;
                
                if (xhr.status === 422 && response && response.errors) {
                    // Validation errors
                    $.each(response.errors, function(field, messages) {
                        var input = $('#' + field);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(messages[0]);
                    });
                    
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please check your passwords and try again.'
                    });
                } else {
                    // Other errors
                    var errorMessage = response && response.message ? response.message : 'Current password is incorrect!';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage
                    });
                }
            },
            complete: function() {
                $('#passwordForm button[type="submit"]').prop('disabled', false).html('<i class="bx bx-key me-1"></i>Update Password');
            }
        });
    });

    // Avatar Upload
    $('#avatarInput').change(function() {
        var file = this.files[0];
        if (file) {
            console.log('Avatar file selected:', file.name); // Debug log
            
            var formData = new FormData();
            formData.append('avatar', file);
            
            $.ajax({
                url: "{{ route('profile.upload-avatar') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    // Show loading on avatar
                    $('.avatar-upload').html('<span class="spinner-border spinner-border-sm text-white"></span>');
                },
                success: function(response) {
                    console.log('Avatar upload response:', response); // Debug log
                    
                    if (response.success) {
                        $('#currentAvatar').attr('src', response.avatar_url + '?t=' + Date.now()); // Cache busting
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message || 'Failed to upload image!'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Avatar upload error:', xhr, status, error); // Debug log
                    
                    var response = xhr.responseJSON;
                    var errorMessage = response && response.message ? response.message : 'Failed to upload image!';
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Failed!',
                        text: errorMessage
                    });
                },
                complete: function() {
                    // Restore camera icon
                    $('.avatar-upload').html('<i class="bx bx-camera text-white" style="font-size: 14px;"></i>');
                }
            });
        }
    });

    // Delete Account Form
    $('#deleteAccountForm').submit(function(e) {
        e.preventDefault();
        console.log('Delete account form submitted'); // Debug log
        
        var formData = $(this).serialize();
        
        // Clear errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        $.ajax({
            url: "{{ route('profile.destroy') }}",
            type: 'POST',
            data: formData + '&_method=DELETE',
            beforeSend: function() {
                $('#deleteAccountForm button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Deleting...');
            },
            success: function(response) {
                console.log('Delete account response:', response); // Debug log
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Account Deleted!',
                        text: response.message,
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = response.redirect || '/';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Failed to delete account!'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log('Delete account error:', xhr, status, error); // Debug log
                
                var response = xhr.responseJSON;
                
                if (xhr.status === 422 && response && response.errors) {
                    // Validation errors
                    if (response.errors.password) {
                        $('#delete_password').addClass('is-invalid');
                        $('#delete_password').siblings('.invalid-feedback').text(response.errors.password[0]);
                    }
                } else {
                    var errorMessage = response && response.message ? response.message : 'Something went wrong!';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage
                    });
                }
            },
            complete: function() {
                $('#deleteAccountForm button[type="submit"]').prop('disabled', false).html('<i class="bx bx-trash me-1"></i>Delete My Account');
            }
        });
    });
});

// Toggle Password Visibility
function togglePassword(fieldId) {
    var field = $('#' + fieldId);
    var icon = $('#' + fieldId + '_icon');
    
    if (field.attr('type') === 'password') {
        field.attr('type', 'text');
        icon.removeClass('bx-show').addClass('bx-hide');
    } else {
        field.attr('type', 'password');
        icon.removeClass('bx-hide').addClass('bx-show');
    }
}

// Confirm Delete Account
function confirmDeleteAccount() {
    Swal.fire({
        title: 'Are you absolutely sure?',
        text: 'This will permanently delete your account and all associated data. This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete my account!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $('#deleteAccountModal').modal('show');
        }
    });
}

// Resend Email Verification
function resendVerification() {
    Swal.fire({
        icon: 'info',
        title: 'Feature Coming Soon',
        text: 'Email verification resend feature will be available soon.'
    });
}
</script>
@endpush