# AGENTS.md - import-product-grouped-ee

## Zweck & Verantwortung

Das `import-product-grouped-ee` Modul bietet **EE-spezifische Grouped Product Import-Funktionalität** mit Staging und Sequence-Management. Es ist ein **Tier 6 Modul** in der EE-Import-Hierarchie und erweitert das `import-product-grouped` Modul mit Enterprise Edition Features.

**Hauptverantwortung:**
- EE Grouped Product Staging Support (zukünftige Grouped-Updates)
- EE Sequence Actions für Audit-Trail Grouped-Imports
- Observer Pattern Integration mit EE Hooks
- Staging-Table Management für Grouped Products
- Version und Timeline Management
- Product Link Staging Koordination

**Modul-Kategorie:** EE Extension Module  
**Komplexität:** ⭐⭐⭐ (Mittel - weniger komplex als Bundle-EE)  
**Abhängig von:** Magento EE Enterprise Edition

## Architektur & Design Patterns

### Kern-Klassen
- **EeGroupedRepository**: EE Grouped-spezifische Persistierung mit Staging
- **StagingGroupedRepository**: Staging-Table Management für Grouped Products
- **SequenceActionRepository**: Audit-Trail für Grouped-Imports
- **EeGroupedProcessor**: Service Layer für EE Grouped-Verarbeitung
- **EeGroupedObserver**: Observer für EE Lifecycle Hooks
- **GroupedStagingManager**: Koordiniert Staging für Grouped Link Tables

### Verwendete Patterns
- **Observer Pattern**: Integration mit Parent Grouped Import Hooks
- **Repository Pattern**: Abstraktion der Staging-Datenschicht
- **Service Layer Pattern**: EE-spezifische Business Logic
- **Staging Pattern**: Zeitgesteuerte Link-Updates
- **Decorator Pattern**: Erweiterung der Base Grouped Repositories

### Staging-Datenfluss
```
Grouped CSV (mit Datum/Zeit)
    ↓
Parser + Converter
    ↓
EE Grouped Processor
    ├─→ StagingGroupedRepository (in link_staging)
    ├─→ SequenceActionRepository (Audit-Trail)
    └─→ GroupedStagingManager (Timing für Links)
    ↓
Magento Database (catalog_product_link_staging)
    ↓
Scheduler aktualisiert catalog_product_link zum Zeitstempel
```

## Abhängigkeiten

### Externe Pakete
- **Keine direkten PHP-Pakete**

### TechDivision Dependencies
- **import-product-ee** ^27.0.0 - EE Product Importer (Base)
- **import-product-grouped** ^20.0.0 - Grouped Product Importer (Parent)
- **import-product-link-ee** - EE Link Import Framework
- **import-converter-ee** - EE Conversion Framework

### Abhängig von diesem Modul (1 Reverse Dependency)
- **import-cli-simple** - Master CLI für alle Importer

### Magento EE Dependencies
- **Magento_Staging** - Core Staging Framework
- **Magento_Enterprise** - EE License Check

## Wichtige Entry Points

### Repository Klassen
```php
// EE Grouped Repository - mit Staging-Support
EeGroupedRepository::create($row): void
EeGroupedRepository::findByProductIdAndStaging($productId, $stagingId): GroupedStaging

// Staging Grouped Repository - Staging-Tabellen-Verwaltung
StagingGroupedRepository::create($row, $stagingData): void
StagingGroupedRepository::findByStagingId($stagingId): array

// Sequence Action Repository - Audit-Trail
SequenceActionRepository::createAction($groupedId, $action): void
```

### Observer Methods
- `EeGroupedObserver::handle()` - Haupteingangspunkt für EE-Integration
- `EeGroupedObserver::handleGroupedStaging()` - Staging-spezifische Logik
- `EeGroupedObserver::createSequenceAction()` - Audit-Trail Record

## Events & Extension Points

**Erbt Parent Events** aus import-product-grouped, erweitert um EE-spezifische

### Observer Hooks
- `product.import.grouped.staging.validate.pre` - Vor Staging-Validierung
- `product.import.grouped.link.staging.process.post` - Nach Link-Staging
- `product.import.grouped.sequence.action.create` - Audit-Trail Record
- `product.import.grouped.staging.schedule.post` - Nach Scheduling

## Database Schema

### EE-Staging-Tabellen
- **catalog_product_link_staging** - Grouped Link Staging (wie catalog_product_link)
  - `product_id` (Parent)
  - `linked_product_id` (Child)
  - `link_type_id` (3 = Grouped)
  - `created_in`, `updated_in` - Staging Timeline
  
- **catalog_product_link_attribute_staging** - Link Attribute Staging (Quantities)
  - `product_link_attribute_id`
  - `product_link_id`
  - `value` (Quantity)
  - `created_in`, `updated_in` - Staging Timeline

### Audit-Trail Tabellen
- **sequence_product_ee** - Sequence für Grouped Imports
  - `sequence_id`, `grouped_product_id`, `action_type`
  - `created_at`, `import_batch_id`

## Common Use Cases

### Use Case 1: Zukünftige Grouped-Link Änderungen
```php
// CSV mit Staging-Datum:
// sku,grouped_sku,grouped_qty,staging_from_date

// BUNDLE-PROD,CHILD-1,5,2026-04-15 14:00:00
// BUNDLE-PROD,CHILD-2,3,2026-04-15 14:00:00

// Importer erstellt:
// 1. Links in catalog_product_link_staging
// 2. created_in/updated_in = 2026-04-15 14:00:00
// 3. Scheduler aktiviert zum Zeitstempel
```

### Use Case 2: EE Versioning für Grouped Products
```php
// Nach Grouped Import mit Staging wird Versioning erstellt
// sequence_product_ee Eintrag:
// - grouped_product_id: 456
// - action_type: 'UPDATE'
// - import_batch_id: 'GROUPED_2026_04_15'
```

## Performance Considerations

### Wichtige Performance-Aspekte
1. **Staging-Overhead**: Schreib in Staging-Tabellen zusätzlich zu Live-Tabellen
2. **Link Staging**: catalog_product_link_staging hat viele Zeilen
3. **Timeline Indizes**: created_in/updated_in MUSS auf staging-Tabellen indexiert sein
4. **Attribute Staging**: Quantities sind in separate Staging-Tabelle

### Optimierungen
- Batch Staging-Inserts (max 1000 Links pro Batch)
- Nutze Transaktionen für Consistency zwischen live/staging
- Cleanup alte Staging-Links nach Schedule-Execution
- Cache Product-IDs während Staging-Verarbeitung

### Speicher-Optimierung
- Streame große Grouped-Staging-Operationen
- Cleanup alte Link-Staging-Daten
- Archiviere alte Sequence-Records

## Hints für KI-Agenten

### Kritisches Verständnis
1. **Tier 6 Modul**: EE-spezifische Extension des Grouped Importers
2. **Staging-fokussiert**: Arbeitet mit zukünftigen Timelines
3. **Link-Staging**: Staging für catalog_product_link Tabelle
4. **Observer Pattern**: Integration in Parent Grouped Import
5. **Audit-Trail**: Sequencing für Compliance

### Häufige Fehler
- ❌ Staging-Tabellen ignorieren
- ❌ catalog_product_link_attribute_staging nicht aktualisieren
- ❌ Timeline-Indizes nicht beachten
- ❌ Sequence-Actions nicht erstellen
- ❌ Transaktionen nicht nutzen

### Best Practices
- ✅ Nutze Staging-Repositories statt direkter DB-Zugriffe
- ✅ Erstelle Sequence-Actions für Audit-Trails
- ✅ Nutze Transaktionen für Multi-Table Updates
- ✅ Validiere Staging-Termine VOR Persistierung
- ✅ Implementiere Cleanup für alte Staging-Links

## Known Limitations

- **EE-Only**: Funktioniert nur auf Magento EE Deployments
- **Staging-Abhängig**: Erfordert dass Magento_Staging aktiviert ist
- **Link-Only**: Arbeitet nur mit catalog_product_link
- **Timeline-Restriktionen**: created_in muss größer als updated_in sein
- **Keine Rollback**: Staging nur über Scheduler rückgängig machbar

## Related Modules

### Direct Dependencies
- **import-product-grouped** - Base Grouped Product Importer
- **import-product-link-ee** - EE Link Import Framework

### Related/Companion Modules
- **import-product-bundle-ee** - EE Bundle Product Importer
- **import-product-variant-ee** - EE Configurable Product Importer
- **import-product-ee** - Base EE Product Importer

## Troubleshooting

### Problem: Staging-Links werden nicht aktiv
**Mögliche Ursachen:**
1. Magento_Staging nicht aktiviert
2. Scheduler läuft nicht
3. Timeline falsch konfiguriert

**Lösung:**
- Prüfe dass Magento_Staging aktiviert ist
- Validiere dass Scheduler/Cron läuft
- Prüfe dass created_in in Zukunft liegt

### Problem: Quantities werden nicht übernommen
**Mögliche Ursachen:**
1. catalog_product_link_attribute_staging nicht gefüllt
2. Attribute-ID nicht korrekt

**Lösung:**
- Validiere dass Link Attributes im Staging gespeichert werden
- Prüfe dass product_link_attribute_id korrekt ist

### Problem: Audit-Trail fehlt
**Mögliche Ursachen:**
1. Sequence-Action Repository nicht registriert
2. Observer nicht augelöst

**Lösung:**
- Prüfe dass Observer in di.xml registriert ist
- Validiere dass Sequence-Action-Creation im Observer läuft

## Zusammenfassung

`import-product-grouped-ee` ist ein **Tier 6 EE-Modul**, das Enterprise Edition Features für Grouped Product Import mit Staging und Audit-Trails bietet. Es erweitert den Base Grouped Importer um Link-Staging und Versioning.

**Für KI-Agenten:** Verstehe dieses Modul als:
- **EE Grouped Product Importer** mit Link-Staging Support
- **Tier 6 Extension** mit Timeline-Management
- **Staging-fokussiert** für zukünftige Link-Updates
- **Audit-Trail Integration** für Tracking und Compliance
