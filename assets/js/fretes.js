
document.addEventListener("DOMContentLoaded", function () {
    const produtos = document.querySelectorAll(
        'input[name="produtos[]"]'
    );

    produtos.forEach(function (produto) {
        produto.addEventListener("change", function () {
            const quantidade = document.querySelector(
                'input[name="quantidades[' + produto.value + ']"]'
            );

            if (quantidade) {
                quantidade.disabled = !produto.checked;

                if (!produto.checked) {
                    quantidade.value = 1;
                }
            }
        });
    });
});
