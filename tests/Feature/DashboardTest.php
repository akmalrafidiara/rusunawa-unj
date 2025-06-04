<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get('/managers');
    $response->assertRedirect('/login');
});

test('authenticated users can visit the managers', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/managers');
    $response->assertStatus(200);
});
