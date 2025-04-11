
@extends('layouts.components.master_detail_livewire')



@section('title', $title)

@section('header')
    @include('partials.product_form', ['product' => $product])
@endsection


@section('detail')
    <livewire:product-color-grid :product-id="$product->id" />
@endsection
