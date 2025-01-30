@extends('index')


@section('content')
    <div class="row my-5">
        <div class="col-12 text-center">
            <form id="video-upload-form" enctype="multipart/form-data">
                <input type="file" name="video" id="video-file-input">
                <button type="submit">Upload Video</button>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const form = document.getElementById('video-upload-form');
        form.addEventListener('submit', (event) => {
            event.preventDefault();

            const videoFile = document.getElementById('video-file-input').files[0];

            const chunkSize = 1024 * 1024 * 2; // 5 MB chunk size
            let currentOffset = 0;
            let currentChunkIndex = 0;
            const totalChunks = Math.ceil(videoFile.size / chunkSize);

            uploadChunk(currentChunkIndex, currentOffset);


            function uploadChunk(currentChunkIndex, currentOffset) {
                if (currentChunkIndex >= totalChunks) {
                    console.log('Video uploaded successfully');
                    return;
                }

                const chunk = videoFile.slice(currentOffset, currentOffset + chunkSize);

                const formData = new FormData();
                formData.append('file', videoFile);
                formData.append('chunk', chunk);
                formData.append('currentChunkIndex', currentChunkIndex);
                formData.append('totalChunks', totalChunks);

                var xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route('chunk.store') }}');
                xhr.setRequestHeader('X-CSRF-TOKEN', "{{ csrf_token() }}");
                xhr.upload.onprogress = (event) => {
                    if (event.lengthComputable) {
                        const progress = (event.loaded / event.total) * 100;
                        console.log(`Upload progress: ${progress}%`);
                    }
                };
                xhr.onload = () => {
                    if (xhr.status === 200) {
                        currentChunkIndex++;
                        currentOffset += chunkSize;
                        uploadChunk(currentChunkIndex, currentOffset);
                    } else {
                        console.error('Video upload failed');
                        return 0;
                    }
                };
                xhr.send(formData);
            };

            const stopButton = document.getElementById('stop-button');
            stopButton.addEventListener('click', () => {
                // Stop the upload
                xhr.abort();
            });

            const retryButton = document.getElementById('retry-button');
            retryButton.addEventListener('click', () => {
                // Retry the upload from the current chunk index
                currentChunkIndex = Math.min(currentChunkIndex, totalChunks);
                uploadChunk();
            });

        });
    </script>
@endsection
