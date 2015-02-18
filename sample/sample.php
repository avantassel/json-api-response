<?php

use Neomerx\JsonApi\Encoder;
use Neomerx\JsonApi\Settings\Settings;
use Neomerx\JsonApi\Schema\ProviderBase;
use Neomerx\JsonApi\Schema\ProviderContainer;

require './../vendor/autoload.php';

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

$container = new ProviderContainer([
    Post::class    => PostProvider::class,
    Author::class  => AuthorProvider::class,
    Comment::class => CommentProvider::class,
]);

$encoder = new Encoder(Settings::defaults(), $container);

// use $encoder->encode($post) for non-formatted output
echo $encoder->encode($post, null, JSON_PRETTY_PRINT);