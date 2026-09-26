<?php

namespace App\Repositories\Contracts;

use App\Models\ReviewInvitation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ReviewInvitationRepositoryInterface
{
    /**
     * Look an invitation up by the plain token from the emailed link.
     */
    public function findByToken(string $token): ?ReviewInvitation;

    /**
     * @param  array{status?: string, search?: string}  $filters
     */
    public function paginateForAdmin(array $filters, int $perPage = 20): LengthAwarePaginator;
}
