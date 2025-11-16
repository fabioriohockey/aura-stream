# Plano Backend - Dorama Streaming

## **Estrutura do Sistema**

### **1. Modelos de Dados**

#### **User Model**
```php
- id
- name
- email
- password
- email_verified_at
- subscription_type (free/paid)
- subscription_expires_at
- episodes_watched_today
- last_watch_date
- created_at
- updated_at
```

#### **Plan Model**
```php
- id
- name (Free/Premium)
- price (0.00/29.90)
- episodes_per_day (1/unlimited)
- can_download (false/true)
- max_quality (720p/4K)
- has_ads (true/false)
```

#### **Subscription Model**
```php
- id
- user_id
- plan_id
- status (active/cancelled/expired)
- started_at
- expires_at
- payment_method
```

#### **Dorama Model**
```php
- id
- title
- description
- poster_url
- backdrop_url
- rating
- year
- genres (JSON)
- episodes_count
```

#### **Episode Model**
```php
- id
- dorama_id
- title
- episode_number
- duration_minutes
- video_url
- created_at
```

#### **WatchHistory Model**
```php
- id
- user_id
- episode_id
- watched_at
- watch_time_percentage
```

### **2. Planos de Assinatura**

#### **GRÁTIS (Free)**
- **R$ 0,00/mês**
- 1 episódio por dia
- Qualidade 720p
- Com anúncios
- Não pode fazer download
- Lista pessoal limitada (10 doramas)

#### **PREMIUM (Pago)**
- **R$ 29,90/mês**
- Episódios ilimitados
- Qualidade até 4K
- Sem anúncios
- Download offline permitido
- Lista pessoal ilimitada
- Recomendações personalizadas

### **3. API Endpoints**

#### **Autenticação**
```
POST /api/auth/register
POST /api/auth/login
POST /api/auth/logout
POST /api/auth/refresh-token
POST /api/auth/forgot-password
POST /api/auth/reset-password
```

#### **Perfil do Usuário**
```
GET /api/user/profile
PUT /api/user/profile
GET /api/user/subscription
GET /api/user/watch-history
GET /api/user/my-list
POST /api/user/my-list
DELETE /api/user/my-list/{dorama_id}
```

#### **Conteúdo**
```
GET /api/doramas (listagem com paginação)
GET /api/doramas/{id} (detalhes)
GET /api/doramas/{id}/episodes
GET /api/episodes/{id}/watch (verifica permissão)

Resposta quando usuário não pode assistir (exemplos):

```
HTTP 403
{
	"success": false,
	"reason": "login_required", // login_required|episode_inactive|premium_required|daily_limit_reached
	"message": "Você precisa estar logado para assistir este episódio.",
	"action": "login", // sugestão de ação: login|upgrade|none
	"remaining_episodes_today": 0
}
```

E no endpoint de detalhes do episódio `/api/episodes/{id}` será retornado também:

```
"user_info": {
	"can_watch": false,
	"can_watch_reason": "daily_limit_reached",
	"can_watch_message": "Você atingiu o limite diário de episódios grátis. Faça upgrade.",
	"remaining_episodes_today": 0,
}
```
POST /api/episodes/{id}/watch (registra visualização)
```

#### **Planos e Pagamentos**
```
GET /api/plans
POST /api/subscribe
POST /api/cancel-subscription
GET /api/payment-methods
```

### **4. Regras de Negócio**

#### **Limite de Episódios (Plano Free)**
- Usuário free pode assistir **1 episódio por dia**
- Contador reinicia à meia-noite (horário do servidor)
- Tentativa de assistir +1 episódio = mensagem de upgrade
- Episódio parcialmente assistido = conta como 1

#### **Assinatura Premium**
- Pagamento mensal recorrente
- Cancelamento a qualquer momento
- Acesso continua até o fim do período pago
- Upgrade imediato após confirmação de pagamento

### **5. Integração de Pagamento**

#### **Mercado Pago / Stripe**
- Checkout seguro via API
- Webhooks para confirmação
- Tratamento de falhas e chargebacks
- Fatura automática mensal

#### **Fluxo de Upgrade**
1. Usuário clica em "Assinar Premium"
2. Redirecionado para pagamento
3. Pagamento confirmado → webhook
4. Sistema atualiza subscription
5. Acesso liberado automaticamente

### **6. Segurança**

#### **JWT Tokens**
- Access token: 7 dias
- Refresh token: 30 dias
- blacklist de tokens revogados

#### **Rate Limiting**
- Login: 5 tentativas por 15 minutos
- API: 100 requisições por minuto
- Watch endpoint: 10 por minuto

#### **Validações**
- Email único no cadastro
- Senha mínima 8 caracteres
- Verificação de email obrigatória

### **7. Implementação - Ordem Prioridade**

1. **Setup básico** (Migration, Models)
2. **Auth básico** (Register/Login JWT)
3. **Middleware de limite** (episódios por dia)
4. **Endpoints de conteúdo** (listagem/detalhes)
5. **Sistema de planos** (básico funcional)
6. **Integração pagamento** (Mercado Pago/Stripe)
7. **Watch history** (controle de visualização)

---

## **Próximos Passos**

1. ✅ **Criar este plano de arquitetura**
2. 🔄 **Implementar auth básico (register/login)**
3. ⏳ **Middleware de limite de episódios**
4. ⏳ **Endpoints de doramas**
5. ⏳ **Sistema de planos**
6. ⏳ **Integração com pagamento**

---

**Status**: Planejamento completo ✓ | **Próximo**: Implementar auth básico