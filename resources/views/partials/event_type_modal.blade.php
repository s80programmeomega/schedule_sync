<!-- Create Event Type Modal -->
    <div class="modal fade" id="createEventModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Create New Event Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label for="eventName" class="form-label">Event Name</label>
                        <input type="text" class="form-control" id="eventName"
                            placeholder="e.g., 30 Minute Meeting">
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="eventDuration" class="form-label">Duration</label>
                            <select class="form-select" id="eventDuration">
                                <option value="15">15 minutes</option>
                                <option value="30" selected>30 minutes</option>
                                <option value="45">45 minutes</option>
                                <option value="60">60 minutes</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="eventLocation" class="form-label">Location</label>
                            <select class="form-select" id="eventLocation">
                                <option value="zoom">Zoom Meeting</option>
                                <option value="google">Google Meet</option>
                                <option value="phone">Phone Call</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="eventDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="eventDescription" rows="3"
                            placeholder="Brief description about this meeting type"></textarea>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Date Range</label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Start Date">
                                <span class="input-group-text">to</span>
                                <input type="text" class="form-control" placeholder="End Date">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Availability</label>
                            <div class="d-flex">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" value="" id="monday"
                                        checked>
                                    <label class="form-check-label" for="monday">
                                        Mon
                                    </label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" value="" id="tuesday"
                                        checked>
                                    <label class="form-check-label" for="tuesday">
                                        Tue
                                    </label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" value="" id="wednesday"
                                        checked>
                                    <label class="form-check-label" for="wednesday">
                                        Wed
                                    </label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" value="" id="thursday"
                                        checked>
                                    <label class="form-check-label" for="thursday">
                                        Thu
                                    </label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="checkbox" value="" id="friday"
                                        checked>
                                    <label class="form-check-label" for="friday">
                                        Fri
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Time Slots</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" placeholder="9:00 AM">
                                <span class="input-group-text">to</span>
                                <input type="text" class="form-control" placeholder="5:00 PM">
                            </div>
                            <button class="btn btn-sm btn-outline-primary">+ Add Another Time Range</button>
                        </div>

                        <div class="col-md-6">
                            <label for="bufferTime" class="form-label">Buffer Time</label>
                            <select class="form-select" id="bufferTime">
                                <option value="0">No buffer time</option>
                                <option value="5">5 minutes before</option>
                                <option value="10" selected>10 minutes before</option>
                                <option value="15">15 minutes before</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Advanced Settings</label>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="hideEventType">
                            <label class="form-check-label" for="hideEventType">Hide event type</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="requireConfirmation">
                            <label class="form-check-label" for="requireConfirmation">Require confirmation before
                                scheduling</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="limitEvents">
                            <label class="form-check-label" for="limitEvents">Limit the number of events per
                                day</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Create Event Type</button>
                </div>
            </div>
        </div>
    </div>
