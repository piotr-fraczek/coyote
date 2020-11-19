<?php

/** @var $this \Illuminate\Routing\Router */
$this->group(['namespace' => 'Forum', 'prefix' => 'Forum', 'as' => 'forum.'], function () {
    // strona glowna forum
    $this->get('/', ['uses' => 'HomeController@index', 'as' => 'home']);

    $this->post('Preview', ['uses' => 'SubmitController@preview', 'as' => 'preview']);

    $this->get('Tag/{tag_name}', ['uses' => 'HomeController@tag', 'as' => 'tag']);
    $this->post('Tag/Save', ['uses' => 'TagController@save', 'as' => 'tag.save']);
    $this->get('Tag/Prompt', ['uses' => 'TagController@prompt', 'as' => 'tag.prompt']);
    $this->get('Tag/Validate', ['uses' => 'TagController@valid', 'as' => 'tag.validate']);
    $this->get('Categories', ['uses' => 'HomeController@categories', 'as' => 'categories']);
    $this->get('All', ['uses' => 'HomeController@all', 'as' => 'all']);
    $this->get('Mine', ['uses' => 'HomeController@mine', 'as' => 'mine', 'middleware' => 'auth']);
    $this->get('Subscribes', ['uses' => 'HomeController@subscribes', 'as' => 'subscribes', 'middleware' => 'auth']);
    $this->get('User/{id}', ['uses' => 'HomeController@user', 'as' => 'user']);
    $this->get('Interesting', ['uses' => 'HomeController@interesting', 'as' => 'interesting']);
    $this->post('Mark', ['uses' => 'HomeController@mark', 'as' => 'mark']);

    // dodawanie zalacznika do posta
    $this->post('Upload', ['uses' => 'AttachmentController@upload', 'as' => 'upload']);
    // sciaganie zalacznika
    $this->get('Download/{attachment}', ['uses' => 'AttachmentController@download', 'as' => 'download']);
    // wklejanie zdjec przy pomocy Ctrl+V w textarea
    $this->post('Paste', ['uses' => 'AttachmentController@paste', 'as' => 'paste']);

    // Add or edit topic's post
    // ----------------------------------------------------
    $this->get('{forum}/Submit/{topic}/{post?}', [
        'uses' => 'SubmitController@index',
        'as' => 'post.submit',
        'middleware' => [
            // topic.access must be first
            'topic.access', 'can:access,forum', 'forum.write'
        ]
    ]);

    $this->post('{forum}/Submit/{topic}/{post?}', [
        'uses' => 'SubmitController@save',
        'middleware' => [
            // topic.access must be first
            'topic.access', 'can:access,forum', 'forum.write', 'throttle:10,1'
        ]
    ]);

    // Add new topic
    // -------------------------------------------------
    $this->get('{forum}/Submit/{topic?}', [
        'uses' => 'SubmitController@index',
        'as' => 'topic.submit',
        'middleware' => [
            'can:access,forum', 'forum.write', 'forum.url'
        ]
    ]);

    $this->post('{forum}/Submit/{topic?}', [
        'uses' => 'SubmitController@save',
        'middleware' => [
            'can:access,forum', 'forum.write', 'forum.url', 'throttle:10,1'
        ]
    ]);

    // Change topic's subject
    // ----------------------------------------------
    $this->post('Topic/Subject/{topic}', [
        'uses' => 'SubmitController@subject',
        'as' => 'topic.subject',
        'middleware' => 'auth'
    ]);

    $this->post('{forum}/Mark', [
        'uses' => 'CategoryController@mark',
        'as' => 'category.mark',
        'middleware' => 'can:access,forum'
    ]);

    // obserwowanie danego watku na forum
    $this->post('Topic/Subscribe/{topic}', [
        'uses' => 'TopicController@subscribe',
        'as' => 'topic.subscribe',
        'middleware' => 'auth'
    ]);

    // blokowanie watku
    $this->post('Topic/Lock/{topic}', ['uses' => 'LockController@index', 'as' => 'lock', 'middleware' => 'auth']);
    // podpowiadanie nazwy uzytkownika (w kontekscie danego watku)
    $this->get('Topic/Prompt/{id?}', ['uses' => 'TopicController@prompt', 'as' => 'prompt']);
    // przeniesienie watku do innej kategorii
    $this->post('Topic/Move/{topic}', ['uses' => 'MoveController@index', 'as' => 'move']);
    // oznacz watek jako przeczytany
    $this->post('Topic/Mark/{topic}', ['uses' => 'TopicController@mark', 'as' => 'topic.mark']);

    // dziennik zdarzen dla watku
    $this->get('Stream/{topic}', ['uses' => 'StreamController@index', 'as' => 'stream', 'middleware' => ['auth']]);

    // widok kategorii forum
    $this->get('{forum}', [
        'uses' => 'CategoryController@index',
        'as' => 'category',
        'middleware' => ['can:access,forum', 'forum.url']
    ]);

    // usuwanie posta
    $this->delete('Post/Delete/{post}', [
        'uses' => 'DeleteController@index',
        'as' => 'post.delete',
        'middleware' => 'auth'
    ]);

    // przywracanie posta
    $this->post('Post/Restore/{id}', [
        'uses' => 'RestoreController@index',
        'as' => 'post.restore',
        'middleware' => 'auth'
    ]);

    // obserwowanie posta
    $this->post('Post/Subscribe/{post}', [
        'uses' => 'PostController@subscribe',
        'as' => 'post.subscribe',
        'middleware' => 'auth'
    ]);

    // glosowanie na dany post
    $this->post('Post/Vote/{post}', ['uses' => 'VoteController@index', 'as' => 'post.vote']);
    $this->get('Post/Voters/{post}', ['uses' => 'VoteController@voters']);
    // akceptowanie danego posta jako poprawna odpowiedz w watku
    $this->post('Post/Accept/{post}', ['uses' => 'AcceptController@index', 'as' => 'post.accept', 'middleware' => 'auth']);
    // historia edycji danego posta
    $this->get('Post/Log/{post}', ['uses' => 'LogController@log', 'as' => 'post.log']);
    // przywrocenie poprzedniej wersji posta
    $this->post('Post/Rollback/{post}/{id}', ['uses' => 'RollbackController@rollback', 'as' => 'post.rollback']);
    // mergowanie posta z poprzednim
    $this->post('Post/Merge/{post}', ['uses' => 'MergeController@index', 'as' => 'post.merge']);

    // edycja/publikacja komentarza oraz jego usuniecie
    $this->post('Comment/{comment?}', [
        'uses' => 'CommentController@save',
        'as' => 'comment.save',
        'middleware' => ['auth']
    ]);

    $this->delete('Comment/Delete/{comment}', [
        'uses' => 'CommentController@delete',
        'as' => 'comment.delete',
        'middleware' => ['auth']
    ]);

    $this->get('Comment/Show/{post}', [
        'uses' => 'CommentController@show',
        'as' => 'comment.show'
    ]);

    // glosowanie w ankiecie
    $this->post('Poll/{poll}', [
        'uses' => 'PollController@vote',
        'as' => 'poll.vote',
        'middleware' => [
            'auth'
        ]
    ]);

    // change category order
    $this->post('Setup', ['uses' => 'CategoryController@setup', 'middleware' => 'auth']);
    $this->post('{forum}/Collapse', ['uses' => 'CategoryController@collapseSection', 'as' => 'section']);

    // Show topic
    // -------------------------------------------------------
    $this->get('{forum}/{topic}-{slug?}', [
        'uses' => 'TopicController@index',
        'as' => 'topic',
        'middleware' => [
            'topic.access', 'can:access,forum', 'topic.scroll', 'page.hit', 'json'
        ]
    ]);

    // skrocony link do posta
    $this->get('{id}', ['uses' => 'ShareController@index', 'as' => 'share']);
});
