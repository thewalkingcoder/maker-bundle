# TwcMakerBundle

[![Build Status](https://travis-ci.com/thewalkingcoder/maker-bundle.svg?branch=master)](https://travis-ci.com/thewalkingcoder/maker-bundle)

When your symfony architecture is different like (ddd, cqrs, adr, custom architecture) you lost the powerfull of makerBundle,
because directory structure is not at the same place.
TwcMakerBundle try to revolve that, to wrap maker command and introduce "context" option.

## Installation

```bash
composer require twc/maker-bundle --dev
```

## Basic Usage

### Generic configuration

```yaml
#config/packages/dev/twc_maker.yml
twc_maker:
    componentName:
        - { context: 'contextName', target: 'Your\SpecificNamespace' }
        - { context: 'contextName1', target: 'Your\SpecificNamespace' }
        - ...
```

### Specific configuration

#### Component entity

for entity component you must use ***target_entity*** and ***target_repository*** instead ***target***

```yaml
#config/packages/dev/twc_maker.yaml
twc_maker:
    entity:
        - { context: 'contextName', target_entity: 'Your\SpecificEntityNamespace', target_repository: 'Your\SpecificRepositoryNamespace' }
```

#### Component controller

for controller you can specific ***dir*** to change generation folder (default contextName)

```yaml
#config/packages/dev/twc_maker.yaml
twc_maker:
    controller:
        - { context: 'contextName', target: 'Your\SpecificNamespace', dir: 'my/custom/directory/template' }
```

### Console

TwcMakerBundle wrap maker command and add new option ***--context*** (shortcut -c)

### Sample with CQRS concept

```yaml
#config/packages/dev/twc_maker.yaml
twc_maker:
    message:
        - { context: 'post.command', target: 'App\Post\Application\Command' }
        - { context: 'post.query', target: 'App\Post\Application\Query' }
```

in your console

```bash
php bin/console make:twc:message NewPost --context=post.command
php bin/console make:twc:message AllPostsArchivedQuery --context=post.query
```

```bash
#shortcut version
php bin/console make:twc:message NewPost -c post.command
php bin/console make:twc:message AllPostsArchivedQuery -c post.query

```
results

```bash
created: src/Post/Application/Command/NewPost.php
created: src/Post/Application/Command/NewPostHandler.php
created: src/Post/Application/Query/AllPostArchivedQuery.php
created: src/Post/Application/Query/AllPostArchivedQueryHandler.php

```

### Sample with DDD concept

```yaml
#config/packages/dev/twc_maker.yaml
twc_maker:
    entity:
        - { context: 'membership', target_entity: 'App\MemberShip\Domain\Entity', target_repository: 'App\MemberShip\Infrastructure\Doctrine\Repository' }
    controller:
        - { context: 'membership', target: 'App\MemberShip\Presenter\Controller' }
    form:
        - { context: 'membership', target: 'App\MemberShip\Presenter\Form' }
```

in your console

```bash
php bin/console make:twc:entity UserMembership -c membership
```

result

```bash
created: src/Membership/Domain/Entity/UserMemberShip.php
created: src/Membership/Infrastructure/Doctrine/Repository/UserMemberShipRepository.php
```

## Support

Actually TwcMakerBundle wrap 9 components

| components |
|------------|
| controller |
| validator  |
| form |
| message |
| messenger-middleware |
| voter |
| command |
| fixtures |
| entity |



