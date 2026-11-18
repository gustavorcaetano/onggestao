function formatarCPF(v) {
    v = v.replace(/\D/g, "").slice(0, 11);
    v = v.replace(/(\d{3})(\d)/, "$1.$2");
    v = v.replace(/(\d{3})(\d)/, "$1.$2");
    v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
    return v;
}

function formatarTelefone(v) {
    v = v.replace(/\D/g, "").slice(0, 13);
    if (v.length === 0) return "";
    let formato = "+";
    if (v.length > 0) formato += v.substring(0, 2);
    if (v.length > 2) formato += " (" + v.substring(2, 4);
    if (v.length > 4) formato += ") " + v.substring(4, 9);
    if (v.length > 9) formato += "-" + v.substring(9, 13);
    return formato;
}

function formatarCEP(v) {
    v = v.replace(/\D/g, "").slice(0, 8);
    return v.replace(/^(\d{5})(\d)/, "$1-$2");
}

function formatarRenda(v) {
    v = v.replace(/\D/g, "");
    if (v === "") return "";
    let numero = (parseFloat(v) / 100).toFixed(2);
    let partes = numero.split(".");
    let inteiro = partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    return `R$ ${inteiro},${partes[1]}`;
}

// Delegação de eventos: funciona para qualquer input presente ou gerado via loop/modal
document.addEventListener("input", function (e) {
    const el = e.target;
    if (!el) return;

    if (el.classList.contains("mask-cpf") || el.name === "cpf") {
        el.value = formatarCPF(el.value);
    } else if (el.classList.contains("mask-telefone") || el.name === "telefone") {
        el.value = formatarTelefone(el.value);
    } else if (el.classList.contains("mask-cep") || el.name === "cep") {
        el.value = formatarCEP(el.value);
    } else if (el.classList.contains("mask-renda") || el.name === "renda_familiar") {
        el.value = formatarRenda(el.value);
    }
});

// Formata automaticamente valores já preenchidos ao carregar a página (ex: modais de edição)
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('input[name="cpf"], .mask-cpf').forEach(el => {
        if (el.value) el.value = formatarCPF(el.value);
    });
    document.querySelectorAll('input[name="telefone"], .mask-telefone').forEach(el => {
        if (el.value) el.value = formatarTelefone(el.value);
    });
    document.querySelectorAll('input[name="cep"], .mask-cep').forEach(el => {
        if (el.value) el.value = formatarCEP(el.value);
    });
    document.querySelectorAll('input[name="renda_familiar"], .mask-renda').forEach(el => {
        if (el.value) {
            // Se vier do banco como "1200.00", converte para centavos antes de formatar
            let val = el.value.includes(".") && !el.value.includes(",") 
                ? parseFloat(el.value).toFixed(2).replace(".", "") 
                : el.value;
            el.value = formatarRenda(val);
        }
    });
});