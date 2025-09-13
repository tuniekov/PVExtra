# Установка и настройка галереи файлов gtsAPIFile

## Обзор

Галерея файлов gtsAPIFile - это полнофункциональное решение для управления файлами в MODX с интеграцией в PVTables. Система позволяет загружать, просматривать, редактировать файлы и привязывать их к ресурсам и пользователям.

## Структура файлов

### Серверная часть (PHP)
```
V:\OSPanel\home\modx28.loc\public\Extras\gtsAPI\
├── core/components/gtsapi/model/gtsapi/
│   └── gtsapifile.class.php                    # Модель файлов
├── core/components/gtsapi/api_controllers/
│   └── filegallery.class.php                   # API контроллер
└── _build/resolvers/
    └── tables.php                              # Регистрация API (обновлен)
```

### Клиентская часть (Vue.js)
```
V:\OSPanel\home\modx28.loc\public\Extras\PVTables\
├── src/components/gtsAPIFileGallery/
│   ├── FileGallery.vue                         # Основной компонент галереи
│   ├── FileUploadDialog.vue                    # Диалог загрузки
│   ├── FileEditDialog.vue                      # Диалог редактирования
│   ├── FileViewDialog.vue                      # Диалог просмотра
│   └── index.js                                # Экспорт и API клиент
├── src/index.js                                # Интеграция с PVTables (обновлен)
├── docs/
│   ├── file-gallery-usage.md                  # Документация по использованию
│   └── file-gallery-installation.md           # Данная инструкция
└── examples/
    └── file-gallery-example.html              # Примеры использования
```

## Установка

### 1. Обновление схемы базы данных

Убедитесь, что в файле схемы gtsAPI добавлена таблица `gtsapi_files`:

```xml
<!-- V:\OSPanel\home\modx28.loc\public\Extras\gtsAPI\core\components\gtsapi\model\schema\gtsapi.mysql.schema.xml -->
<object class="gtsAPIFile" table="gtsapi_files" extends="xPDOSimpleObject">
    <field key="parent" dbtype="int" precision="10" phptype="integer" null="false" default="0" index="index"/>
    <field key="class" dbtype="varchar" precision="100" phptype="string" null="false" default="modResource" index="index"/>
    <field key="list" dbtype="varchar" precision="100" phptype="string" null="false" default="default" index="index"/>
    <field key="name" dbtype="varchar" precision="255" phptype="string" null="false" default=""/>
    <field key="description" dbtype="text" phptype="string" null="true"/>
    <field key="path" dbtype="varchar" precision="255" phptype="string" null="false" default=""/>
    <field key="file" dbtype="varchar" precision="255" phptype="string" null="false" default=""/>
    <field key="mime" dbtype="varchar" precision="100" phptype="string" null="false" default=""/>
    <field key="type" dbtype="varchar" precision="100" phptype="string" null="false" default=""/>
    <field key="trumb" dbtype="varchar" precision="255" phptype="string" null="true"/>
    <field key="url" dbtype="varchar" precision="255" phptype="string" null="false" default=""/>
    <field key="hash" dbtype="varchar" precision="40" phptype="string" null="true"/>
    <field key="session" dbtype="varchar" precision="40" phptype="string" null="true"/>
    <field key="size" dbtype="bigint" precision="20" phptype="integer" null="false" default="0"/>
    <field key="createdby" dbtype="int" precision="10" phptype="integer" null="false" default="0"/>
    <field key="source" dbtype="int" precision="10" phptype="integer" null="false" default="1"/>
    <field key="context" dbtype="varchar" precision="100" phptype="string" null="false" default="web"/>
    <field key="active" dbtype="tinyint" precision="1" phptype="boolean" null="false" default="1"/>
    <field key="rank" dbtype="int" precision="10" phptype="integer" null="false" default="0"/>
    <field key="createdon" dbtype="datetime" phptype="datetime" null="true"/>
    <field key="properties" dbtype="text" phptype="json" null="true"/>

    <index alias="parent" name="parent" primary="false" unique="false" type="BTREE">
        <column key="parent" length="" collation="A" null="false"/>
    </index>
    <index alias="class" name="class" primary="false" unique="false" type="BTREE">
        <column key="class" length="" collation="A" null="false"/>
    </index>
    <index alias="list" name="list" primary="false" unique="false" type="BTREE">
        <column key="list" length="" collation="A" null="false"/>
    </index>
    <index alias="active" name="active" primary="false" unique="false" type="BTREE">
        <column key="active" length="" collation="A" null="false"/>
    </index>
    <index alias="hash" name="hash" primary="false" unique="false" type="BTREE">
        <column key="hash" length="" collation="A" null="false"/>
    </index>

    <aggregate alias="Parent" class="gtsAPIFile" local="parent" foreign="id" cardinality="one" owner="foreign"/>
    <composite alias="Children" class="gtsAPIFile" local="id" foreign="parent" cardinality="many" owner="local"/>
</object>
```

### 2. Переустановка пакета gtsAPI

После добавления файлов выполните переустановку пакета gtsAPI:

1. Зайдите в админку MODX
2. Перейдите в "Управление пакетами"
3. Найдите пакет gtsAPI
4. Нажмите "Переустановить"

Это создаст таблицу `gtsapi_files` и зарегистрирует API endpoint `file-gallery`.

### 3. Настройка медиа-источников

Убедитесь, что настроен медиа-источник для загрузки файлов:

1. В админке MODX перейдите в "Медиа → Источники медиа"
2. Создайте или настройте источник медиа
3. Укажите путь для загрузки файлов (например, `assets/uploads/gallery/`)
4. Настройте права доступа

### 4. Настройка прав доступа

Создайте необходимые разрешения в MODX:

1. Перейдите в "Безопасность → Права доступа"
2. Создайте разрешения:
   - `file_manager` - общий доступ к файлам
   - `file_upload` - загрузка файлов
   - `file_edit` - редактирование файлов
   - `file_delete` - удаление файлов
3. Назначьте разрешения соответствующим группам пользователей

### 5. Обновление PVTables

Убедитесь, что в проекте PVTables подключены новые компоненты галереи файлов. Файл `src/index.js` уже обновлен для включения компонентов галереи.

## Проверка установки

### 1. Проверка API

Проверьте доступность API endpoint:

```bash
# Получение списка файлов
GET /api/file-gallery?action=list

# Ответ должен быть:
{
  "success": 1,
  "message": "",
  "data": {
    "files": [],
    "total": 0,
    "limit": 20,
    "offset": 0
  }
}
```

### 2. Проверка компонентов Vue

Проверьте, что компоненты доступны в Vue приложении:

```javascript
// В консоли браузера
console.log(window.Vue.options.components.FileGallery)
// Должен вернуть объект компонента
```

### 3. Тестовое использование

Создайте простую страницу для тестирования:

```vue
<template>
  <div>
    <h1>Тест галереи файлов</h1>
    <FileGallery
      title="Тестовая галерея"
      :parent-id="1"
      parent-class="modResource"
      list-name="test"
    />
  </div>
</template>
```

## Настройка

### Конфигурация API

Настройки API контроллера можно изменить в файле `filegallery.class.php`:

```php
$this->config = array_merge([
    'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'zip', 'rar'],
    'max_file_size' => 10485760, // 10MB
    'upload_path' => 'uploads/gallery/',
], $config);
```

### Настройка миниатюр

Для настройки генерации миниатюр отредактируйте свойства медиа-источника:

```json
{
  "imageThumbnails": [
    {"w": 120, "h": 90, "q": 90, "zc": 1},
    {"w": 300, "h": 200, "q": 85, "zc": 1}
  ],
  "thumbnailType": "jpg",
  "thumbnailName": "{name}.{rand}.{w}.{h}.{ext}"
}
```

### Настройка прав доступа

В файле `tables.php` можно изменить права доступа к API:

```php
[
    'point' => 'file-gallery',
    'controller_class'=>'fileGalleryAPIController',
    'controller_path'=>'[[+core_path]]components/gtsapi/api_controllers/filegallery.class.php',
    'authenticated'=>1,
    'groups'=>'Administrator,Editor',  // Группы с доступом
    'permitions'=>'file_manager',      // Необходимые разрешения
    'active'=>1,
],
```

## Использование

### Базовое использование

```vue
<template>
  <FileGallery
    :parent-id="resourceId"
    parent-class="modResource"
    list-name="gallery"
  />
</template>

<script>
export default {
  data() {
    return {
      resourceId: 123
    }
  }
}
</script>
```

### Программная работа с API

```javascript
import { FileGalleryAPI } from 'pvtables'

const api = new FileGalleryAPI('/api/file-gallery')

// Загрузка файлов
const files = await api.getFiles({
  parent: 123,
  class: 'modResource',
  list: 'gallery'
})

// Загрузка файла
const result = await api.uploadFiles([file], {
  parent: 123,
  class: 'modResource',
  list: 'gallery'
})
```

## Устранение неполадок

### Ошибка "Не удалось инициализировать источник медиа"

1. Проверьте настройки медиа-источника
2. Убедитесь, что путь существует и доступен для записи
3. Проверьте права доступа к папке

### Ошибка "Нет прав доступа"

1. Проверьте, что пользователь авторизован
2. Убедитесь, что у пользователя есть необходимые разрешения
3. Проверьте настройки групп пользователей

### Файлы не загружаются

1. Проверьте размер файла (не превышает лимит)
2. Убедитесь, что расширение файла разрешено
3. Проверьте настройки PHP (upload_max_filesize, post_max_size)
4. Проверьте логи ошибок MODX

### Миниатюры не создаются

1. Убедитесь, что установлено расширение GD или ImageMagick
2. Проверьте права доступа к папке кеша
3. Проверьте настройки phpThumb в MODX

## Безопасность

1. **Валидация файлов** - всегда проверяйте типы и размеры файлов
2. **Права доступа** - используйте систему разрешений MODX
3. **Санитизация** - очищайте имена файлов от опасных символов
4. **Изоляция** - храните загруженные файлы отдельно от исполняемых
5. **Мониторинг** - ведите логи загрузок и доступа к файлам

## Производительность

1. **Кеширование** - используйте кеширование для миниатюр
2. **CDN** - рассмотрите использование CDN для статических файлов
3. **Оптимизация** - сжимайте изображения при загрузке
4. **Пагинация** - используйте пагинацию для больших списков файлов
5. **Индексы** - убедитесь, что созданы необходимые индексы в БД

## Поддержка

При возникновении проблем:

1. Проверьте логи ошибок MODX
2. Убедитесь, что все файлы установлены корректно
3. Проверьте настройки прав доступа
4. Обратитесь к документации MODX и gtsAPI

## Обновления

При обновлении системы:

1. Сделайте резервную копию базы данных
2. Сохраните настройки конфигурации
3. Обновите файлы компонентов
4. Переустановите пакет gtsAPI
5. Проверьте работоспособность
