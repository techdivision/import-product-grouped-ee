# AGENTS.md - import-product-grouped-ee

## Zweck & Verantwortung

Das `import-product-grouped-ee` Modul bietet **EE-spezifische Grouped Product Import-Funktionalität**. Es ist ein **Tier 6 Modul** und erweitert `import-product-grouped`.

**Hauptverantwortung:**
- EE Grouped Product Staging Support
- EE Sequence Actions für Grouped Products
- Observer Pattern für EE Grouped-Hooks

## Architektur & Design Patterns

### Kern-Klassen
- **EeGroupedObserver**: Observer für EE-Hooks

### Verwendete Patterns
- **Observer Pattern**: Für EE-Hooks

## Abhängigkeiten

### Externe Pakete
- **Keine**

### TechDivision Dependencies
- **import-product-ee** ^27.0.0 - EE Product Importer
- **import-product-grouped** ^20.0.0 - Grouped Product Importer

### Abhängig von diesem Modul (1 Reverse Dependency)
- **import-cli-simple** - Master CLI

## Wichtige Entry Points

### Observer Klassen
```php
// EE Grouped Observer
EeGroupedObserver::handle($row): void
```

## Events & Extension Points

**Keine Events** - Tier 6 EE-Modul

## Hints für KI-Agenten

### Wichtig zu verstehen
1. **Tier 6 Modul**: Erweitert Grouped Product Importer mit EE-Features
2. **EE-fokussiert**: Spezialisiert auf EE Staging
3. **Observer Pattern**: Für EE-Hooks

## Bekannte Einschränkungen

- **EE-Only**: Nur für Magento EE Deployments
- **Grouped-EE-Only**: Nur für EE Grouped Products

## Zusammenfassung

`import-product-grouped-ee` ist ein **Tier 6 Modul**, das EE-spezifische Grouped Product Import-Funktionalität bietet. Es erweitert den Grouped Product Importer mit EE-Features.

**Für Agenten:** Verstehe dieses Modul als **EE Grouped Product Importer** mit Observer Pattern.
