const imageInput = document.getElementById('avatar_path');

const imageMinWidth = 200;
const imageMinHeight = 200;
const imageMaxWidth = 800;
const imageMaxHeight = 800;

imageInput.addEventListener('input', () => {
    const file = imageInput.files[0];
    const reader = new FileReader();
    reader.addEventListener('load', () => {
        _validateImage(reader.result).then((result) => {
            // TODO обработка error
            if (result)
            {
                document.getElementById('avatar_preview').src = reader.result;
            }
            else
            {
                imageInput.value = null
                alert('Файл слишком большой или слишком маленький.\n\n' +
                    'Используйте файл не меньше 200*200 и не больше 800*800');
            }
        })
    }, false);
    // TODO onerror

    if (file)
    {
        reader.readAsDataURL(file);
    }
});

async function _validateImage(fileSrc)
{
    const promise = new Promise(resolve => {
        const image = new Image();
        image.addEventListener('load', () => {
            resolve(imageMinWidth <= image.width <= imageMaxWidth
                && imageMinHeight <= image.height <= imageMaxHeight);
        }, false);
        // TODO: нужен onerror -> reject
        image.src = fileSrc;
    });
    return promise.then((result) => {return result});
}