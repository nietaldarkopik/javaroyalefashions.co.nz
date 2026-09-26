<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * A review invitation couldn't be created or sent. The message is safe to
 * show to the admin as-is.
 */
class ReviewInvitationException extends RuntimeException {}
