{{--
 * Ce fichier fait partie du projet Lys secure
 * Copyright (C) 2024 Floris Robart <florobart.github@gmail.com>
--}}

<div class="colCenterContainer">
    @if ($errors->any())
        <div class="rowCenterContainer">
            <ul>
                @foreach ($errors->all() as $error)
                    <li class="normalTextError text-center">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rowCenterContainer">
            <span class="normalTextError text-center">{{ session()->get('error') }}</span>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="rowCenterContainer">
            <span class="normalTextValid text-center">{{ session()->get('success') }}</span>
        </div>
    @endif

    @if (session()->has('message'))
        <div class="rowCenterContainer">
            <span class="normalTextAlert text-center">{{ session()->get('message') }}</span>
        </div>
    @endif
</div>
