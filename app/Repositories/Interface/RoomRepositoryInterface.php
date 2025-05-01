<?php

namespace App\Repositories\Interface;

interface RoomRepositoryInterface
{
    public function getRooms($request);

    public function getRoomsDatatable($request);
    
    public function paginateRooms(int $perPage = 6);

}
