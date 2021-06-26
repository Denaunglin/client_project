@extends('backend.admin.layouts.app')

@section('meta_title', 'Edit Admin User')
@section('page_title')
@lang("message.header.edit_admin_user")
@endsection
@section('page_title_icon')
<i class="metismenu-icon pe-7s-users"></i>
@endsection

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.admin-users.update', ['admin_user' => $admin_user->id]) }}" method="post"
                    id="form">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">@lang("message.name")</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    value="{{$admin_user->name}}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">@lang("message.email")</label>
                                <input type="email" name="email" id="email" class="form-control"
                                    value="{{$admin_user->email}}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">@lang("message.password")</label>
                                <input type="password" name="password" id="password" class="form-control" value="">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role_id">@lang("message.roles")</label>
                                <select class="form-control select2" name="roles[]" id="roles" multiple>
                                    @foreach($roles as $role)
                                    <option value="{{$role->name}}" @if( in_array($role->name,
                                        $admin_user->getRoleNames()->toArray()) ) selected @endif>{{$role->name}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.admin-users.index') }}" class="btn btn-danger mr-5">@lang("message.cancel")</a>
                            <input type="submit" value="@lang("message.update")" class="btn btn-success">
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
{!! JsValidator::formRequest('App\Http\Requests\UpdateAdminUser', '#form') !!}
@endsection
