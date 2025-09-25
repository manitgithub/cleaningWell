// invoice-form.js
var invoiceItems = [];
var itemIndex = 0;

$(document).ready(function() {
    // เพิ่มรายการแรกถ้าไม่มี
    if (invoiceItems.length === 0) {
        addItem();
    }
    
    // Auto-generate invoice code
    generateInvoiceCode();
    
    // Set default date to today
    if (!$('#invoice-date').val()) {
        $('#invoice-date').val(new Date().toISOString().split('T')[0]);
    }
    
    // Set default due date to 30 days from today
    if (!$('#invoice-due_date').val()) {
        var dueDate = new Date();
        dueDate.setDate(dueDate.getDate() + 30);
        $('#invoice-due_date').val(dueDate.toISOString().split('T')[0]);
    }
    
    // Set default VAT rate
    if (!$('#invoice-vat_rate').val()) {
        $('#invoice-vat_rate').val('7');
    }
    
    // Set default WHT rate
    if (!$('#invoice-wht_rate').val()) {
        $('#invoice-wht_rate').val('3');
    }
});

function generateInvoiceCode() {
    // Generate code format: IV202409XXXX
    var today = new Date();
    var year = today.getFullYear();
    var month = String(today.getMonth() + 1).padStart(2, '0');
    var timestamp = Date.now().toString().slice(-4);
    
    var code = 'IV' + year + month + timestamp;
    $('#invoice-code').val(code);
}

function addItem() {
    var item = {
        description: '',
        qty: 1,
        unit: 'ชิ้น',
        unit_price: 0,
        line_discount: 0,
        line_total: 0
    };
    
    invoiceItems.push(item);
    renderItems();
}

function removeItem(index) {
    if (invoiceItems.length > 1) {
        invoiceItems.splice(index, 1);
        renderItems();
        calculateTotals();
    } else {
        alert('ต้องมีรายการอย่างน้อย 1 รายการ');
    }
}

function renderItems() {
    var html = '';
    
    for (var i = 0; i < invoiceItems.length; i++) {
        html += '<tr data-index="' + i + '">';
        html += '<td>';
        html += '<input type="text" name="items[' + i + '][description]" class="form-control" ';
        html += 'value="' + (invoiceItems[i].description || '') + '" ';
        html += 'onchange="updateItem(' + i + ', \'description\', this.value)" required>';
        html += '</td>';
        
        html += '<td>';
        html += '<input type="number" name="items[' + i + '][qty]" class="form-control text-center" ';
        html += 'value="' + invoiceItems[i].qty + '" step="0.01" min="0" ';
        html += 'onchange="updateItem(' + i + ', \'qty\', parseFloat(this.value) || 0)" required>';
        html += '</td>';
        
        html += '<td>';
        html += '<input type="text" name="items[' + i + '][unit]" class="form-control text-center" ';
        html += 'value="' + (invoiceItems[i].unit || '') + '" ';
        html += 'onchange="updateItem(' + i + ', \'unit\', this.value)" required>';
        html += '</td>';
        
        html += '<td>';
        html += '<input type="number" name="items[' + i + '][unit_price]" class="form-control text-right" ';
        html += 'value="' + invoiceItems[i].unit_price + '" step="0.01" min="0" ';
        html += 'onchange="updateItem(' + i + ', \'unit_price\', parseFloat(this.value) || 0)" required>';
        html += '</td>';
        
        html += '<td>';
        html += '<input type="number" name="items[' + i + '][line_discount]" class="form-control text-right" ';
        html += 'value="' + invoiceItems[i].line_discount + '" step="0.01" min="0" ';
        html += 'onchange="updateItem(' + i + ', \'line_discount\', parseFloat(this.value) || 0)">';
        html += '</td>';
        
        html += '<td>';
        html += '<input type="text" name="items[' + i + '][line_total]" class="form-control text-right" ';
        html += 'value="' + number_format(invoiceItems[i].line_total, 2) + '" readonly>';
        html += '</td>';
        
        html += '<td class="text-center">';
        html += '<button type="button" class="btn btn-danger btn-sm" onclick="removeItem(' + i + ')">';
        html += '<i class="fas fa-trash"></i>';
        html += '</button>';
        html += '</td>';
        html += '</tr>';
    }
    
    $('#items-container').html(html);
}

function updateItem(index, field, value) {
    if (invoiceItems[index]) {
        invoiceItems[index][field] = value;
        
        // คำนวณยอดรวมของแต่ละรายการ
        var qty = parseFloat(invoiceItems[index].qty) || 0;
        var unitPrice = parseFloat(invoiceItems[index].unit_price) || 0;
        var discount = parseFloat(invoiceItems[index].line_discount) || 0;
        
        invoiceItems[index].line_total = (qty * unitPrice) - discount;
        
        // อัพเดทการแสดงผล
        var row = $('tr[data-index="' + index + '"]');
        row.find('input[name="items[' + index + '][line_total]"]').val(number_format(invoiceItems[index].line_total, 2));
        
        calculateTotals();
    }
}

function calculateTotals() {
    var subTotal = 0;
    var totalDiscount = 0;
    
    // คำนวณรวมก่อนลดและส่วนลดรวม
    for (var i = 0; i < invoiceItems.length; i++) {
        var qty = parseFloat(invoiceItems[i].qty) || 0;
        var unitPrice = parseFloat(invoiceItems[i].unit_price) || 0;
        var discount = parseFloat(invoiceItems[i].line_discount) || 0;
        
        subTotal += (qty * unitPrice);
        totalDiscount += discount;
    }
    
    var vatRate = parseFloat($('#invoice-vat_rate').val()) || 0;
    var whtRate = parseFloat($('#invoice-wht_rate').val()) || 0;
    
    var netAmount = subTotal - totalDiscount;
    var vatAmount = (netAmount * vatRate) / 100;
    var whtAmount = (netAmount * whtRate) / 100;
    var grandTotal = netAmount + vatAmount - whtAmount;
    
    // อัพเดทฟิลด์
    $('#invoice-sub_total').val(number_format(subTotal, 2));
    $('#invoice-discount_total').val(number_format(totalDiscount, 2));
    $('#invoice-vat_amount').val(number_format(vatAmount, 2));
    $('#invoice-wht_amount').val(number_format(whtAmount, 2));
    $('#invoice-grand_total').val(number_format(grandTotal, 2));
}

function number_format(number, decimals) {
    return parseFloat(number).toFixed(decimals);
}

// Event handler for form submission
$('#invoice-form').on('submit', function(e) {
    // Validate that we have at least one item
    if (invoiceItems.length === 0) {
        e.preventDefault();
        alert('กรุณาเพิ่มรายการสินค้า/บริการอย่างน้อย 1 รายการ');
        return false;
    }
    
    // Validate that all items have description
    var hasEmptyDescription = false;
    for (var i = 0; i < invoiceItems.length; i++) {
        if (!invoiceItems[i].description || invoiceItems[i].description.trim() === '') {
            hasEmptyDescription = true;
            break;
        }
    }
    
    if (hasEmptyDescription) {
        e.preventDefault();
        alert('กรุณากรอกรายละเอียดสินค้า/บริการให้ครบถ้วน');
        return false;
    }
    
    return true;
});
