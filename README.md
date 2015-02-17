# JSON API responses

[![Version](https://img.shields.io/packagist/v/neomerx/json-api-response.svg)](https://packagist.org/packages/neomerx/json-api-response)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/neomerx/json-api-response/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/neomerx/json-api-response/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/neomerx/json-api-response/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/neomerx/json-api-response/?branch=master)
[![Build Status](https://travis-ci.org/neomerx/json-api-response.svg?branch=master)](https://travis-ci.org/neomerx/json-api-response)
[![HHVM](https://img.shields.io/hhvm/neomerx/json-api-response.svg)](https://travis-ci.org/neomerx/json-api-response)
[![License](https://img.shields.io/packagist/l/neomerx/json-api-response.svg)](https://packagist.org/packages/neomerx/json-api-response)

Framework agnostic [JSON API](http://jsonapi.org/) response implementation.

This package covers encoding PHP objects to JavaScript Object Notation (JSON) as described in [JSON API Document Structure](http://jsonapi.org/format/#document-structure).

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
$ composer require neomerx/json-api-response ~1.0
```

## Usage

Assuming you have the following classes in your project

``` php
class Post
{
    public $id;
    public $title;
    public $body;
    public $author;
    private $comments;

    public function __construct($id, $title, $body, Author $author, array $comments)
    {
        $this->id       = $id;
        $this->title    = $title;
        $this->body     = $body;
        $this->author   = $author;
        $this->comments = $comments;
    }

    public function getComments()
    {
        return $this->comments;
    }
}

class Author
{
    public $id;
    public $name;

    public function __construct($id, $name)
    {
        $this->id   = $id;
        $this->name = $name;
    }
}

class Comment
{
    public $id;
    public $body;

    public function __construct($id, $body)
    {
        $this->id   = $id;
        $this->body = $body;
    }
}
```

And the following data you want to encode to JSON API format

``` php
$post = new Post(
    '1',
    'Lorem ipsum dolor sit amet',
    'Mauris cursus, nisi quis pulvinar maximus, dui orci hendrerit felis.',
    new Author('3', 'John Author'),
    [
        new Comment('7', 'Morbi rutrum libero urna, quis ullamcorper nisi mollis in.'),
        new Comment('9', 'In aliquam nec felis eu venenatis. Nam vulputate, est.'),
    ]
);
```

You will create classes that provide descriptions for your data types

``` php
class PostProvider extends ProviderBase
{
    public function __construct()
    {
        parent::__construct('id', 'http://example.com/posts/', 'posts');
    }

    public function getAttributes($post)
    {
        return [
            'title' => $post->title,
            'body'  => $post->body,
        ];
    }

    public function getLinks($post)
    {
        return [
            'author'   => $post->author,
            'comments' => $post->getComments(),
        ];
    }
}

class AuthorProvider extends ProviderBase
{
    public function __construct()
    {
        parent::__construct('id', 'http://example.com/author/', 'people');
    }

    public function getAttributes($author)
    {
        return [
            'name' => $author->name,
        ];
    }
}

class CommentProvider extends ProviderBase
{
    public function __construct()
    {
        parent::__construct('id', 'http://example.com/comments/', 'comments');
    }

    public function getAttributes($comment)
    {
        return [
            'body' => $comment->body,
        ];
    }
}
```

And encode the data

``` php
$provider = new ProviderContainer([
    Post::class    => PostProvider::class,
    Author::class  => AuthorProvider::class,
    Comment::class => CommentProvider::class,
]);

$encoder  = new Encoder(Settings::defaults(), $provider);

// use $encoder->encode($post) for non-formatted output
echo $encoder->encode($post, null, JSON_PRETTY_PRINT);
```

The output will be

``` json
{
    "posts": {
        "id": "1",
        "title": "Lorem ipsum dolor sit amet",
        "body": "Mauris cursus, nisi quis pulvinar maximus, dui orci hendrerit felis.",
        "href": "http:\/\/example.com\/posts\/1",
        "links": {
            "author": {
                "id": "3",
                "name": "John Author"
            },
            "comments": [
                {
                    "id": "7",
                    "body": "Morbi rutrum libero urna, quis ullamcorper nisi mollis in."
                },
                {
                    "id": "9",
                    "body": "In aliquam nec felis eu venenatis. Nam vulputate, est."
                }
            ]
        }
    }
}
```

Full source code for this sample is [here](/sample/)

Every aspect of output could be customized with `Settings` class. See [SettingsInterface](src/Contracts/SettingsInterface.php) for details.

## Questions?

Do not hesitate to contact us on [@twitter](https://twitter.com/NeomerxCom) or post an [issue](https://github.com/neomerx/json-api-response/issues).

## Testing

``` bash
$ phpunit
```

## Versioning

The project is using [Semantic Versioning](http://semver.org/).

## Credits

- [Neomerx](https://github.com/neomerx)
- [All Contributors](../../contributors)

## License

Apache License (Version 2.0). Please see [License File](LICENSE) for more information.
