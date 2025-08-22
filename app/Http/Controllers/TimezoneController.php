<?php

namespace App\Http\Controllers;

use App\Models\Timezone;
use App\Http\Requests\StoreTimezoneRequest;
use App\Http\Requests\UpdateTimezoneRequest;

class TimezoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTimezoneRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Timezone $timezone)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Timezone $timezone)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTimezoneRequest $request, Timezone $timezone)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Timezone $timezone)
    {
        //
    }
}
