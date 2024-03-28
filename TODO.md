# Pact Provider Bundle

List of tasks to outline what needs to be done

### Plan
- [ ] Use `Route` attribute
    - [ ] Use `parameter` in `path`
        - [ ] Use condition if can not use `parameter` in `path` https://symfony.com/doc/5.x/routing.html#matching-expressions

- [ ] Update Symfony to 6.4
    - [ ] Use `#[MapRequestPayload]` attribute
        - https://symfony.com/blog/new-in-symfony-6-3-mapping-request-data-to-typed-objects
        - https://blog.redrat.com.br/validating-requests-on-symfony-framework
    - [ ] Validate using `symfony/validator`
    - [ ] Use `#[MapQueryString]` or `#[MapQueryParameter]` attributes?
        - https://symfony.com/blog/new-in-symfony-6-3-query-parameters-mapper

### Check

- [ ] What if
    - [ ] No message return ?
    - [ ] `providerStates` empty?

### Completed ✓
- [x] Update PHP to 8.1
- [x] Update Symfony to 5.4
- [x] Update PHPUnit to 10.1
