@extends('admin.layouts.admin_main')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0">Edit Coordinator</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.coordinators.update', $coordinator->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $coordinator->name) }}" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Designation</label>
                                    <input type="text" name="designation" class="form-control"
                                        value="{{ old('designation', $coordinator->designation) }}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Mobile</label>
                                    <input type="text" name="mobile" class="form-control"
                                        value="{{ old('mobile', $coordinator->mobile) }}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Division</label>
                                    <input type="text" name="division" class="form-control"
                                        value="{{ old('division', $coordinator->division) }}">
                                </div>



                                <div class="col-md-3">
                                    <label class="form-label">Contact Number</label>
                                    <input type="text" name="contact_no" class="form-control"
                                        value="{{ old('contact_no', $coordinator->contact_no) }}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">ORCID</label>
                                    <input type="text" name="orcid" class="form-control"
                                        value="{{ old('orcid', $coordinator->orcid) }}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Facebook</label>
                                    <input type="url" name="facebook" class="form-control"
                                        value="{{ old('facebook', $coordinator->facebook) }}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">LinkedIn</label>
                                    <input type="url" name="linkedin" class="form-control"
                                        value="{{ old('linkedin', $coordinator->linkedin) }}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Twitter</label>
                                    <input type="url" name="twitter" class="form-control"
                                        value="{{ old('twitter', $coordinator->twitter) }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Biography</label>
                                    <textarea name="biography" class="form-control" rows="3">{{ old('biography', $coordinator->biography) }}</textarea>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ route('admin.coordinators.create') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
