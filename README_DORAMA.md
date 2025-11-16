# Dorama Streaming Platform - Guia de Implementação

## 📋 O que foi implementado

### ✅ Backend Completo (Laravel API)

#### **Models e Database:**
- ✅ **Category** - Categorias (Romance, Ação, Comédia, etc.)
- ✅ **Dorama** - Informações dos doramas
- ✅ **Episode** - Episódios com múltiplas qualidades
- ✅ **User** - Sistema de assinatura (free/premium)
- ✅ **WatchHistory** - Histórico de visualização
- ✅ Relacionamentos entre todos os models

#### **Controllers API:**
- ✅ **AuthController** - Login, registro, perfil
- ✅ **DoramaController** - Listar, buscar, detalhes
- ✅ **EpisodeController** - Episódios, próximo/anterior
- ✅ **CategoryController** - Categorias e filtros
- ✅ **StreamController** - Streaming com controle de banda
- ✅ **UploadController** - Upload de vídeos e imagens

#### **Features Implementadas:**
- ✅ Sistema de autenticação com tokens (Sanctum)
- ✅ Controle de episódios gratuitos (1/dia)
- ✅ Assinatura premium (acesso ilimitado)
- ✅ Streaming otimizado para VPS
- ✅ Controle de banda (500kbps free, 2mbps premium)
- ✅ Histórico de visualização
- ✅ Progresso dos episódios
- ✅ Sistema de favoritos
- ✅ Busca e filtros avançados

## 🚀 Como usar

### **1. Upload de Conteúdo**

**Criar estrutura de pastas:**
```bash
POST /api/upload/directories
{
  "dorama_id": 1
}
```

**Upload de poster:**
```bash
POST /api/upload/poster
Content-Type: multipart/form-data
poster: [arquivo.jpg]
dorama_id: 1
```

**Upload de vídeo 480p:**
```bash
POST /api/upload/video/480p
Content-Type: multipart/form-data
video: [video.webm]
episode_id: 1
```

**Upload de vídeo 720p (premium):**
```bash
POST /api/upload/video/720p
Content-Type: multipart/form-data
video: [video_720p.webm]
episode_id: 1
```

### **2. Listar Conteúdo**

**Todos os doramas:**
```bash
GET /api/doramas?page=1&per_page=12&sort=popular&country=Coreia
```

**Doramas em destaque:**
```bash
GET /api/doramas/featured?limit=10
```

**Buscar doramas:**
```bash
GET /api/doramas/search?q=Descendentes%20do%20Sol
```

**Categorias:**
```bash
GET /api/categories
GET /api/categories/romance/doramas
```

### **3. Streaming de Vídeos**

**Informações do episódio:**
```bash
GET /api/stream/1/info
Authorization: Bearer {token}
```

**Stream do vídeo:**
```bash
GET /api/stream/1/480p
Authorization: Bearer {token}
Range: bytes=0-1023
```

**Registrar progresso:**
```bash
POST /api/stream/1/progress
Authorization: Bearer {token}
{
  "progress_seconds": 1200,
  "is_completed": false
}
```

**Histórico de visualização:**
```bash
GET /api/stream/history
Authorization: Bearer {token}
```

### **4. Autenticação**

**Registro:**
```bash
POST /api/auth/register
{
  "name": "João Silva",
  "email": "joao@email.com",
  "password": "12345678",
  "password_confirmation": "12345678"
}
```

**Login:**
```bash
POST /api/auth/login
{
  "email": "joao@email.com",
  "password": "12345678"
}
```

## 📁 Estrutura de Arquivos

```
storage/app/public/doramas/
├── {dorama_id}/
│   ├── poster_{timestamp}.jpg
│   ├── backdrop_{timestamp}.jpg
│   └── episodes/
│       ├── ep1_480p_{timestamp}.webm (150MB)
│       ├── ep1_720p_{timestamp}.webm (300MB)
│       ├── thumb_ep1_{timestamp}.jpg (50KB)
│       └── legendas_ep1_{timestamp}.vtt (5KB)
```

## 🎥 Recomendações de Compressão

**FFmpeg commands para otimização:**
```bash
# 480p ultra-otimizado (~100MB/45min)
ffmpeg -i input.mp4 -c:v libx265 -crf 32 -preset veryfast \
       -c:a aac -b:a 64k -vf scale=854:480 output_480p.webm

# 720p para premium (~300MB/45min)
ffmpeg -i input.mp4 -c:v libx265 -crf 28 -preset veryfast \
       -c:a aac -b:a 128k -vf scale=1280:720 output_720p.webm

# Legendas embutidas
ffmpeg -i input.mp4 -vf "subtitles=legendas.srt" \
       -c:v libx265 -crf 32 output.webm
```

## 📊 Estimativas de Uso

**Armazenamento:**
- 1 dorama (16 episódios): ~2.5GB (480p) ou ~5GB (720p)
- 10 doramas: ~25GB (480p) ou ~50GB (720p)
- +1GB para posters e thumbnails

**Banda (mensal):**
- Usuário free (1 episódio/dia): ~150MB/mês
- Usuário premium (ilimitado): ~4.5GB/mês (1 episódio/dia)

## 🔧 Configuração Adicional

**NGINX para otimizar streaming:**
```nginx
location /storage/videos/ {
    expires 1d;
    add_header Cache-Control "public, immutable";

    # Limitar banda por IP
    limit_rate 500k;

    # Previnir hotlink
    valid_referers none blocked seudominio.com;
    if ($invalid_referer) { return 403; }

    # Suporte a Range requests
    add_header Accept-Ranges bytes;
}
```

**Cache do Laravel:**
```php
// Cache de metadados
Cache::remember("dorama_{$id}", 3600, function() {
    return Dorama::with('episodes')->find($id);
});

// Cache de URLs de vídeos
Cache::remember("video_url_{$episodeId}", 3600, function() {
    return $this->generateSignedUrl($episodeId);
});
```

## 🎯 Próximos Passos Sugeridos

1. **Frontend** - Implementar interface React/Vue
2. **Player de vídeo** - Componente com legendas e controles
3. **Sistema de pagamentos** - Integração com gateway
4. **Painel admin** - Gerenciar conteúdo e usuários
5. **Sistema de recomendação** - Baseado em histórico
6. **API externa** - Integrar com IMDB/TMDB
7. **App mobile** - React Native ou Flutter

## 🛡️ Segurança

- ✅ Tokens Sanctum expiram
- ✅ Proteção contra hotlink
- ✅ Validação de uploads
- ✅ Rate limiting implementado
- ✅ HTTPS obrigatório (produção)

## 📱 Exemplo de Uso Completo

1. **Criar dorama no banco:**
```sql
INSERT INTO doramas (title, slug, description, country, year, episodes_total,
                    duration_minutes, status, rating)
VALUES ('Descendentes do Sol', 'descendentes-do-sol',
        'Drama romântico coreano...', 'Coreia', 2016, 16, 60,
        'finalizado', 8.5);
```

2. **Criar episódios:**
```sql
INSERT INTO episodes (dorama_id, episode_number, title, duration_seconds,
                     video_path_480p, file_size_480p_mb)
VALUES (1, 1, 'Episódio 1', 3600, 'doramas/1/episodes/ep1_480p.webm', 150);
```

3. **Upload dos arquivos via API**
4. **Testar streaming via frontend**

Sua plataforma de streaming de doramas está pronta! 🎬