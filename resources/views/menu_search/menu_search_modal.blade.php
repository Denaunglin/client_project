<!-- Modal -->
<div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchModalLabel">Item Search</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">Search : </span>
                    </div>
                    <input type="text" id="input" class="form-control menu-search-input" placeholder="Type Item Name ..." aria-label="Menu Search" aria-describedby="basic-addon1" autofocus >
                </div>

                <div id="menu-search-result-container" class="menu-search-result-container" hidden>
                    <h4>Result : </h4>
                    <span id="menu-search-result-msg" class="menu-search-result-msg text-danger"></span>
                    <div class="col-md-12 px-0">
                        <ul id="menu-search-result-list" class="list-group menu-search-result-list"></ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>