<?php

namespace Drupal\wicket_create_account;

// https://valuebound.com/resources/blog/how-to-define-an-event-dispatcher-and-subscriber-drupal-8
use Symfony\Component\EventDispatcher\Event;

class WicketCreateAccount extends Event {
  const CREATE_ACCOUNT = 'event.wicket_new_person';

  protected $form_state;

  public function __construct($form_state)
  {
    $this->form_state = $form_state;
  }

  public function get_form_state()
  {
    return $this->form_state;
  }

  public function event_description() {
    return "This event relates to a new person being created in Wicket via the /create-account form";
  }

}
