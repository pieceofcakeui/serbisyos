<div class="modal fade" id="bookingDetailsModal" tabindex="-1" aria-labelledby="bookingDetailsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingDetailsModalLabel">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    style="background-color: #ccc; border-radius: 50%;"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Customer Information</h5>
                        <div class="mb-3">
                            <label class="form-label">Name:</label>
                            <p class="form-control-static" id="modalCustomerName"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone:</label>
                            <p class="form-control-static" id="modalCustomerPhone"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email:</label>
                            <p class="form-control-static" id="modalCustomerEmail"></p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5>Vehicle Information</h5>
                         <div class="mb-3">
                            <label class="form-label">Vehicle Plate Number:</label>
                            <p class="form-control-static" id="modalPlateNumber"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Vehicle Type:</label>
                            <p class="form-control-static" id="modalVehicleType"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Make & Model:</label>
                            <p class="form-control-static" id="modalVehicleMakeModel"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Year:</label>
                            <p class="form-control-static" id="modalVehicleYear"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Transmission:</label>
                            <p class="form-control-static" id="modalTransmission"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fuel Type:</label>
                            <p class="form-control-static" id="modalFuelType"></p>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <h5>Service Details</h5>
                        <div class="mb-3">
                            <label class="form-label">Service Type:</label>
                            <p class="form-control-static" id="modalServiceType"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date and Time:</label>
                            <p class="form-control-static" id="modalPreferredDateTime"></p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5>Additional Information</h5>
                        <div class="mb-3">
                            <label class="form-label">Vehicle Issues:</label>
                            <p class="form-control-static" id="modalVehicleIssues"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Customer Notes:</label>
                            <p class="form-control-static" id="modalCustomerNotes"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="emergencyRequestModal" tabindex="-1" aria-labelledby="emergencyRequestModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emergencyRequestModalLabel">Emergency Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    style="background-color: #ccc; border-radius: 50%;"></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-6">
                        <h5>Contact Information</h5>
                        <div class="mb-3">
                            <label class="form-label">Requester Name:</label>
                            <p class="form-control-static" id="modalEmergencyRequesterName"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone:</label>
                            <p class="form-control-static" id="modalEmergencyPhone"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address:</label>
                            <p class="form-control-static" id="modalEmergencyAddress"></p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5>Vehicle Information</h5>
                        <div class="mb-3">
                            <label class="form-label">Type:</label>
                            <p class="form-control-static" id="modalEmergencyVehicleType"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Model:</label>
                            <p class="form-control-static" id="modalEmergencyVehicleModel"></p>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Issue Details</h5>
                        <div class="mb-3">
                            <label class="form-label">Description:</label>
                            <p class="form-control-static" id="modalEmergencyDescription"></p>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                      <h5>Video</h5>
                        <div class="mb-3">
                            <div class="row g-2" id="modalEmergencyVideo">
                                <div class="col-12">
                                    <div class="video-container ratio ratio-16x9 bg-light rounded">
                                        <div class="d-flex align-items-center justify-content-center h-100">
                                            <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="fas fa-trash-alt fa-2x text-danger"></i>
                </div>

                <h5 class="mb-2 fw-semibold" style="border: none;">Delete Notification</h5>

                <p class="mb-4">Are you sure you want to delete this notification?</p>

                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-outline-secondary text-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete" style="height: 40px; padding: 0 10px;">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
