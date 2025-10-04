@extends('layouts.app')

@section('title', 'Autorização Bancária')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h4 class="mb-0">Autorização Bancária</h4>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <div class="avatar-lg bg-primary rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3">
                            <i class="fas fa-university fa-2x text-white"></i>
                        </div>
                        <h5>{{ $bankName }}</h5>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Esta é uma <strong>simulação</strong> do processo de autorização bancária. 
                        Em ambiente de produção, você seria redirecionado para o site oficial do seu banco.
                    </div>

                    <div class="mb-4">
                        <h6>Permissões solicitadas:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Leitura de contas</li>
                            <li><i class="fas fa-check text-success me-2"></i>Consulta de saldos</li>
                            <li><i class="fas fa-check text-success me-2"></i>Histórico de transações</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <small>
                            <strong>Sistema de Finanças Pessoais</strong> está solicitando acesso aos seus dados bancários.
                            Você pode revogar esta autorização a qualquer momento.
                        </small>
                    </div>

                    <form method="POST" action="{{ route('bank.integration.simulate.authorize') }}">
                        @csrf
                        <input type="hidden" name="consent_id" value="{{ $consentId }}">
                        
                        <div class="d-grid gap-2">
                            <button type="submit" name="action" value="authorize" class="btn btn-success btn-lg">
                                <i class="fas fa-check me-2"></i>Autorizar Conexão
                            </button>
                            <button type="submit" name="action" value="deny" class="btn btn-outline-danger">
                                <i class="fas fa-times me-2"></i>Negar Acesso
                            </button>
                        </div>
                    </form>

                    <div class="mt-4">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Conexão segura e criptografada
                        </small>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="card-title">O que acontece depois da autorização?</h6>
                    <ul class="small text-muted mb-0">
                        <li>Suas contas bancárias serão detectadas automaticamente</li>
                        <li>As transações dos últimos 30 dias serão importadas</li>
                        <li>A sincronização será feita periodicamente</li>
                        <li>Você poderá revisar e categorizar as transações</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection