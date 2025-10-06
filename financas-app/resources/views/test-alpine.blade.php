@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Teste Alpine.js</h1>
    
    <div x-data="{ open: false }" class="mt-4">
        <button @click="open = !open" class="btn btn-primary">
            Clique para testar dropdown
        </button>
        
        <div x-show="open" x-transition class="mt-2 p-3 bg-light border">
            <p>Menu funcionando! Alpine.js está carregado corretamente.</p>
        </div>
    </div>
</div>
@endsection