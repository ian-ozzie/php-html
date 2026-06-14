# HTML Generator

I don't like writing/maintaining raw HTML, this works for me instead

## Laravel/Livewire

Really not sure how stable/well integrated these are. From my initial usage they serve my purposes and let me work within Laravel without having to write blade syntax.

This package doesn't bring along the dependencies for these as it's meant to be thin, the dependencies are the consuming projects concern.

## Tasks

### lint

```bash
composer run lint
```

### format

```bash
composer run lint:fix
```

### test

```bash
composer run lint:stan
composer run test:coverage
```
