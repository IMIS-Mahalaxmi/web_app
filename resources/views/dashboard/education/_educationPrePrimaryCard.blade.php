<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col text-start">
                    <h6 class="mb-0">Pre-Primary + Schools</h6>
                </div>
                <div class="col text-end">
                    <h4 class="mb-0">{{ number_format($prePrimaryTotal) }}</h4>
                </div>
            </div>
            <div class="row g-0 text-center">
                <div class="col-3">
                    <div class="small-box">
                        <small class="text-muted">0-5</small>
                        <div class="fw-bold">{{ number_format($prePrimaryUpTo5) }}</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="small-box">
                        <small class="text-muted">0-8</small>
                        <div class="fw-bold">{{ number_format($prePrimaryUpTo8) }}</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="small-box">
                        <small class="text-muted">0-10</small>
                        <div class="fw-bold">{{ number_format($prePrimaryUpTo10) }}</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="small-box">
                        <small class="text-muted">0-12</small>
                        <div class="fw-bold">{{ number_format($prePrimaryUpTo12) }}</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
