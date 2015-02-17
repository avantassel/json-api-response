# JSON API responses

Framework agnostic [JSON API](http://jsonapi.org/) response implementation.

This package covers transforming PHP objects to JavaScript Object Notation (JSON) as described in [JSON API Document Structure](http://jsonapi.org/format/#document-structure).

Full supported of JSON API Document Structure specification. In particular

 - [Individual Resource Representations](http://jsonapi.org/format/#document-structure-individual-resource-representations)
 - [Resource Collection Representations](http://jsonapi.org/format/#document-structure-resource-collection-representations)
 - [Collection Objects](http://jsonapi.org/format/#document-structure-collection-objects)
 - [Resource Attributes](http://jsonapi.org/format/#document-structure-resource-object-attributes) "id", "type", "href", "links"
 - [Resource Relationships](http://jsonapi.org/format/#document-structure-resource-relationships) both "To-One Relationships" and "To-Many Relationships"
 - [URL Templates](http://jsonapi.org/format/#document-structure-url-templates)
 - [Compound Documents](http://jsonapi.org/format/#document-structure-compound-documents)
 - Arbitrary meta information 
 - Highly configurable representation of all elements

## Install

Via Composer

``` bash
$ composer require neomerx/json-api-response
```

## Testing

``` bash
$ phpunit
```

## Credits

- [Neomerx](https://github.com/neomerx)
- [All Contributors](../../contributors)

## License

Apache License (Version 2.0). Please see [License File](LICENSE) for more information.
