@extends('admin.layouts.master')
@section('admin_content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-4  col-6 d-flex align-items-center">
                            <h2 class="card-title text-capitalize">@lang('backend.badges.header.list')</h2>
                        </div>
                        <div class="col lg 4">
                            <span class="pull-right d-inline-block @if(session('display_type')=='rtl') float-left @else float-right @endif">
                                <a href="{{ route('admin.badge.create') }}"
                                    class="btn waves-effect waves-light btn-rounded btn-outline-success">
                                    <i class="fa fa-plus" aria-hidden="true"></i> @lang('global.button.add_new')
                                </a>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="table-responsive">
                                    <table id="zero_config" class="product-overview v-middle table">
                                        <thead>
                                            <tr>
                                                <th>@lang('backend.badges.label.name')</th>
                                                <th>@lang('backend.badges.label.actions')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($badges as $item)
                                            <tr>
                                                <td>{{ $item->name }}</td>
                                                <td><a href="{{ route('admin.badge.edit', $item->id) }}"
                                                        class="text-inverse p-r-10 ab" data-toggle="tooltip" title=""
                                                        data-original-title="@lang('global.button.edit')"><i
                                                            class="fas fa-pencil-alt text-inverse mr-2"></i></a>
                                                    <a href="javascript:;"
                                                        onclick="confirmDelete('{{ route('admin.badge.delete', $item->id) }}')"
                                                        class="text-inverse" title="" data-toggle="tooltip"
                                                        data-original-title="@lang('global.button.delete')"><i
                                                            class="ti-trash text-danger"></i></a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
