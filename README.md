# 🛒 The Batch Selection Manager

## ⚠️ WARNING: DEVELOPMENT IN PROGRESS (WORK IN PROGRSS)

> **This bundle is currently under active development and is not ready for production use.**  Use for testing and architectural feedback only.

---

## 💡 Introduction and Motivation

The **BatchSelectionBundle** solves a common problem in Symfony administration interfaces: efficiently managing the selection of items across multiple pages or large datasets for bulk actions (e.g., "Select All 10,000 Items and Apply Action").

This bundle focuses on **performance and extensibility** by decoupling the data source (Doctrine, Arrays, Paginators) from the storage mechanism (Session/Database).

## 🚀 Features

* **Cross-Page Selection:** Persistent storage (via Session or other defined storage) of selected item identifiers.
* **Optimized Loading:** Uses specialized `IdentityLoader` services to retrieve identifiers in bulk, minimizing memory usage and database queries.
* **Performance First:** Features highly optimized Doctrine Query processing to fetch identifiers (`SELECT id`) with a single, fast query for large datasets.
* **Pluggable Architecture:** Easy to extend with custom data sources (Loaders) and object types (Normalizers).

---

## 🏗️ Architecture and Extensibility

The bundle is built around three core interfaces that enforce separation of concerns:

### 1. `IdentityLoaderInterface`

The responsibility of the Loader is to efficiently retrieve *all* scalar identifiers (`string|int`) and the total count from a given data source.

| Implementation | Source Type | Optimization |
| :--- | :--- | :--- |
| `DoctrineQueryLoader` | `Doctrine\ORM\Query` | **High:** Modifies DQL for single optimized `SELECT id` and `COUNT` queries. |
| `PagerfantaAdapterLoader`| `Pagerfanta\Adapter\AdapterInterface`| **High:** Delegates to `DoctrineQueryLoader` after safely extracting the underlying QueryBuilder via reflection. |
| `DoctrineCollectionLoader`| `Doctrine\Common\Collections\Collection`| Handles entity relationships, delegating normalization of individual objects. |
| `IterableLoader` | `array` or `\Traversable` | General purpose, relies on `IdentifierResolver`. |

### 2. `IdentifierResolver` & `IdentifierNormalizerInterface`

This chain-of-responsibility pattern handles the critical conversion of complex PHP types (like full Doctrine Entities, UUID Value Objects, etc.) into simple scalar IDs (`string|int`) before storage.

* Allows developers to support custom ID formats by simply tagging a new `Normalizer` service.

### 3. `SelectionStorageInterface`

(To be defined) Handles where the list of selected IDs is actually persisted (e.g., Session, Database table, Redis).

---

## ⚙️ Installation (Conceptual)

```bash
composer require tito10047/batch-selection-bundle
```