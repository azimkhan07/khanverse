<div id="chatPopup" class="chat-popup">

    {{-- Header --}}

    <div class="chat-header">

        <div class="d-flex align-items-center">

            <img id="chatUserImage" src="{{ asset('admin/assets/images/avatar-4.jpg') }}" class="chat-avatar">

            <div class="ms-2">

                <h6 class="mb-0" id="chatUserName">
                    Loading...
                </h6>

                <small class="text-success" id="chatUserStatus">
                    ● Online
                </small>

            </div>

        </div>

        <div>
            <button class="chat-btn" id="minimizeChat">
                <i class="feather icon-minus"></i>
            </button>
            <button class="chat-btn" id="closeChat">
                <i class="feather icon-x"></i>
            </button>

        </div>

    </div>

    {{-- Body --}}

    <div id="chatContent">

        <div class="chat-body" id="chatBody">

            <div class="text-center text-muted mt-5">

                Loading messages...

            </div>

        </div>

        {{-- Footer --}}

        <div class="chat-footer">

            <button class="footer-btn" id="emojiBtn">
                😊
            </button>
            <button class="footer-btn" id="attachmentBtn">
                📎
            </button>
            <input type="file" id="chatAttachment" hidden>
            <input type="text" class="form-control" id="chatMessage" placeholder="Type your message..." autocomplete="off">
            <button class="btn btn-primary ms-2" id="sendMessage">
                <i class="feather icon-send"></i>
            </button>
        </div>
    </div>
</div>
