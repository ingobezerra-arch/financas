# 🏦 Como Conectar com Bancos Reais - Guia Completo

## 📋 Visão Geral

Atualmente o sistema usa **dados fictícios** para demonstração. Para conectar com seus **dados bancários reais**, você precisa seguir alguns passos importantes.

## 🔐 Pré-requisitos OBRIGATÓRIOS

### 1. Registro como TPP (Third Party Provider)
- **Cadastro oficial** no Diretório Central do Open Finance Brasil
- **Aprovação do Banco Central** do Brasil
- **Certificação de segurança** conforme normas regulatórias

### 2. Certificados Digitais
- **Certificado ICP-Brasil válido** para pessoa jurídica
- **Certificados mTLS** específicos para Open Finance
- **Chaves privadas** protegidas e seguras

### 3. Credenciais por Banco
Cada banco possui suas próprias credenciais:
- **Client ID** específico do banco
- **Client Secret** específico do banco
- **URLs de produção** de cada instituição

---

## ⚡ Configuração Rápida

### Passo 1: Verificar Status Atual
```bash
php artisan bank:setup-real --check
```

### Passo 2: Configurar para Produção
```bash
php artisan bank:setup-real
```

### Passo 3: Reiniciar Servidor
```bash
php artisan serve
```

---

## 🛠️ Configuração Manual

### 1. Editar arquivo .env
```env
# Desativar modo simulado
OPEN_FINANCE_SANDBOX=false
OPEN_FINANCE_USE_REAL_APIS=true

# Suas credenciais reais
OPEN_FINANCE_CLIENT_ID=seu-client-id-real
OPEN_FINANCE_CLIENT_SECRET=sua-secret-real

# Certificados (caminhos absolutos)
OPEN_FINANCE_MTLS_CERT=/caminho/completo/para/certificado.pem
OPEN_FINANCE_MTLS_KEY=/caminho/completo/para/chave-privada.pem
```

### 2. Colocar Certificados
```bash
# Criar diretório
mkdir storage/certificates

# Copiar certificados (exemplo)
cp /seu/certificado.pem storage/certificates/
cp /sua/chave.pem storage/certificates/
```

### 3. Limpar Cache
```bash
php artisan config:clear
php artisan route:clear
```

---

## 🏛️ Bancos Suportados

| Banco | Código | Status API Real |
|-------|--------|----------------|
| Banco do Brasil | 001 | ✅ Configurado |
| Santander | 033 | ✅ Configurado |
| Caixa Econômica | 104 | ✅ Configurado |
| Bradesco | 237 | ✅ Configurado |
| Itaú Unibanco | 341 | ✅ Configurado |
| Nubank | 260 | ✅ Configurado |

---

## 🔄 Como Funciona a Transição

### Modo Atual (Simulado)
```
Usuário → Sistema → Dados Fictícios → Interface
```

### Modo Real (Após configuração)
```
Usuário → Sistema → API do Banco → Dados Reais → Interface
```

---

## ⚠️ Considerações Importantes

### Custos
- **Registro como TPP**: Taxas regulatórias
- **Certificados digitais**: Renovação anual
- **Infraestrutura**: Servidores com certificação

### Segurança
- **Dados sensíveis**: Transações bancárias reais
- **Conformidade**: LGPD e normas do BACEN
- **Auditoria**: Logs detalhados obrigatórios

### Limitações
- **Rate Limits**: Cada banco tem limites de requisições
- **Horários**: Alguns bancos têm janelas de manutenção
- **Permissões**: Usuário precisa autorizar no banco

---

## 🧪 Testando a Configuração

### 1. Verificar Configuração
```bash
php artisan bank:setup-real --check
```

### 2. Testar Conexão (exemplo Nubank)
1. Acesse: http://localhost:8000/bank-integrations/create
2. Selecione "Nu Pagamentos (Nubank)"
3. Clique "Conectar Banco"
4. **Será redirecionado para o site REAL do Nubank**
5. Faça login com suas credenciais reais
6. Autorize o acesso
7. Retornará com suas transações reais

---

## 🆘 Solução de Problemas

### Erro: "Certificado inválido"
```bash
# Verificar formato do certificado
openssl x509 -in certificado.pem -text -noout

# Verificar chave privada
openssl rsa -in chave.pem -check
```

### Erro: "Client ID inválido"
- Verificar se as credenciais estão corretas
- Confirmar registro no banco específico
- Checar se o ambiente (sandbox/produção) está correto

### Erro: "Consentimento negado"
- Verificar se o usuário autorizou no banco
- Confirmar se as permissões estão corretas
- Checar logs do sistema

---

## 📞 Suporte

### Para Empresas/Desenvolvedores
Se você representa uma **empresa** ou é um **desenvolvedor profissional** interessado em integrar dados bancários reais:

1. **Consultoria especializada** em Open Finance
2. **Implementação completa** do registro TPP
3. **Certificação e homologação** junto aos bancos
4. **Suporte técnico** contínuo

### Contato para Implementação Profissional
- 📧 Consulte um especialista em Open Finance
- 🏢 Empresas de consultoria fintech
- 📋 Certificadoras digitais ICP-Brasil

---

## ✅ Resumo dos Passos

1. ☐ Registrar como TPP no Open Finance
2. ☐ Obter certificados digitais ICP-Brasil
3. ☐ Conseguir aprovação do Banco Central
4. ☐ Obter credenciais de cada banco desejado
5. ☐ Configurar sistema com `php artisan bank:setup-real`
6. ☐ Testar integração com banco escolhido
7. ☐ Monitorar e manter certificados atualizados

**💡 Dica**: Para fins de **demonstração e aprendizado**, o modo simulado atual é perfeitamente adequado e mostra todas as funcionalidades do sistema!