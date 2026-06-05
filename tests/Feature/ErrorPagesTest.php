<?php

declare(strict_types=1);

test('a missing page renders the themed 404 view', function (): void {
    $response = $this->get('/this-route-does-not-exist-'.uniqid());

    $response->assertNotFound();
    $response->assertSee('404', false);
    $response->assertSee(__('Page not found'));
    $response->assertSee(__('Back to home'));
});
