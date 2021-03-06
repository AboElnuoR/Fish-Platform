@extends('layouts.app')
@section('title') {{ $market->buy_request ?? $buy ? 'طلب شراء' : 'عرض بيع' }} @stop

@section('content')
<section class="form-registration">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-6 col-md-offset-3">
                    <div class="product-img">
                        <h3 class="text-center"> - {{ $market->buy_request ?? $buy ? 'طلب شراء' : 'عرض بيع' }} -</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-md-offset-2">
                @include('layouts.alert')
                <div class="form-wrapper">
                    {!!
                        Form::open([
                            'method' => isset($market) ? 'PUT' : 'POST', 'files' => true,
                            'route' => isset($market) ? [requestUri() . '.update', $market] : requestUri() . '.store',
                        ])
                    !!}
                        <fieldset>
                            {!! Form::hidden('buy_request', $market->buy_request ?? $buy, ['class' => 'form-control']) !!}
                            <!-- Name input-->
                            @if(requestUri() == 'ptools')
                                <div class="form-group overflow-h">
                                    {!! Form::label(
                                        'ptoolType', 'نوع المنتج', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::select(
                                            'ptoolType',
                                            $types->prepend('من فضلك اختار', 0),
                                            $market->ptoolType ?? null,
                                            ['class' => 'form-control']
                                        ) !!}
                                    </div>
                                </div>
                            @endif
                            <div class="form-group overflow-h">
                                {!! Form::label('HSCode_ID', 'نوع الاسماك', ['class' => 'col-md-4 control-label']) !!}
                                <div class="col-md-8">
                                    {!! Form::select(
                                            'HSCode_ID',
                                            $hSCodes->prepend('من فضلك اختار', 0),
                                            $market->HSCode_ID ?? null,
                                            ['class' => 'form-control']
                                        ) !!}
                                </div>
                            </div>
                            <div class="form-group overflow-h">
                                {!! Form::label('pname', requestUri() == 'ptools' ? 'اسم المنتج' : 'الصنف', ['class' => 'col-md-4 control-label']) !!}
                                <div class="col-md-8">
                                    {!! Form::text('pname', $market->pname ?? old('pname'), ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group overflow-h">
                                {!! Form::label('amount', 'الكميه', ['class' => 'col-md-4 control-label']) !!}
                                <div class="col-md-8">
                                    {!! Form::text('amount', $market->amount ?? old('amount'), ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group overflow-h">
                                {!! Form::label(
                                    'startDate',
                                    'تاريخ ' . ($market->buy_request ?? $buy ? 'الطلب' : 'العرض'),
                                    ['class' => 'col-md-4 control-label']) !!}
                                <div class="col-md-8">
                                    {!! Form::date(
                                        'startDate',
                                        $market->user->startDate ?? old('startDate') ?? \Carbon\Carbon::today(),
                                        ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group overflow-h">
                                {!! Form::label(
                                    'endDate',
                                    ($market->buy_request ?? $buy ? 'الطلب' : 'العرض') . ' ساري حتي',
                                    ['class' => 'col-md-4 control-label']) !!}
                                <div class="col-md-8">
                                    {!! Form::date(
                                        'endDate',
                                        $market->user->endDate ?? old('endDate') ?? \Carbon\Carbon::today(),
                                        ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            @if(requestUri() == 'markets')
                                <div class="form-group overflow-h">
                                    {!! Form::label(
                                        'transportDate', 'تاريخ التوريد', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::date(
                                            'transportDate',
                                            $market->transport->transportDate ?? old('transportDate') ?? \Carbon\Carbon::today(),
                                            ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group overflow-h">
                                    {!! Form::label('price', 'السعر المقترح', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text(
                                            'price',
                                            $market->transport->price ?? old('price'),
                                            ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            @endif
                            </div>
                            <div class="form-group overflow-h">
                                {!! Form::label('specs', 'المواصفات', ['class' => 'col-md-4 control-label']) !!}
                                <div class="col-md-8">
                                    {!! Form::text('specs', $market->specs ?? null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group overflow-h">
                                {!! Form::label('photo', 'صورة المنتج', ['class' => 'col-md-4 control-label']) !!}
                                <div class="col-md-8">
                                    {!! Form::file('photo') !!}
                                </div>
                            </div>
                            <div class="button-custmoeize">
                                <div class="form-group overflow-h">
                                    <div class="col-md-8 col-md-offset-4">
                                        {!! Form::submit(
                                            isset($market) ? 'تعديل' : 'اضافة',
                                            ['class' => 'btn btn-primary btn-lg']) !!}
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript" src="{{ url('/js/fish.js') }}"></script>
@stop
