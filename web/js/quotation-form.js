$(document).ready(function() {
    let itemIndex = $('#items-tbody tr').length;

    // Add new item row
    $('#add-item').on('click', function() {
        let template = $('#item-row-template').html();
        template = template.replace(/INDEX/g, itemIndex);
        $('#items-tbody').append(template);
        itemIndex++;
        updateRowEvents();
    });

    // Remove item row
    $(document).on('click', '.remove-item', function() {
        if ($('#items-tbody tr').length > 1) {
            $(this).closest('tr').remove();
            calculateTotals();
            reindexItems();
        }
    });

    // Load item details when item is selected
    $(document).on('change', '.item-select', function() {
        let itemId = $(this).val();
        let row = $(this).closest('tr');
        
        if (itemId) {
            $.ajax({
                url: '/quotations/get-item',
                data: { id: itemId },
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        row.find('.item-description').val(response.data.description || response.data.name);
                        row.find('.item-price').val(response.data.unit_price);
                        calculateLineTotal(row);
                    }
                }
            });
        }
    });

    // Calculate line total when qty, price, or discount changes
    $(document).on('input', '.item-qty, .item-price, .item-discount', function() {
        let row = $(this).closest('tr');
        calculateLineTotal(row);
    });

    // Calculate VAT and WHT when rates change
    $(document).on('input', '#quotation-vat_rate, #quotation-wht_rate, #quotation-discount_total', function() {
        calculateTotals();
    });

    // Load customers when project is selected
    $('#project-select').on('change', function() {
        let projectId = $(this).val();
        if (projectId) {
            // You can implement project-customer relationship here if needed
        }
    });

    function calculateLineTotal(row) {
        let qty = parseFloat(row.find('.item-qty').val()) || 0;
        let price = parseFloat(row.find('.item-price').val()) || 0;
        let discount = parseFloat(row.find('.item-discount').val()) || 0;
        
        let lineTotal = (qty * price) - discount;
        row.find('.item-total').val(lineTotal.toFixed(2));
        
        calculateTotals();
    }

    function calculateTotals() {
        let subTotal = 0;
        
        // Calculate subtotal from all line totals
        $('.item-total').each(function() {
            subTotal += parseFloat($(this).val()) || 0;
        });
        
        $('#quotation-sub_total').val(subTotal.toFixed(2));
        
        // Get rates and discounts
        let discountTotal = parseFloat($('#quotation-discount_total').val()) || 0;
        let vatRate = parseFloat($('#quotation-vat_rate').val()) || 0;
        let whtRate = parseFloat($('#quotation-wht_rate').val()) || 0;
        
        // Calculate amounts
        let afterDiscount = subTotal - discountTotal;
        let vatAmount = (afterDiscount * vatRate) / 100;
        let whtAmount = (afterDiscount * whtRate) / 100;
        let grandTotal = afterDiscount + vatAmount - whtAmount;
        
        $('#quotation-vat_amount').val(vatAmount.toFixed(2));
        $('#quotation-wht_amount').val(whtAmount.toFixed(2));
        $('#quotation-grand_total').val(grandTotal.toFixed(2));
    }

    function reindexItems() {
        $('#items-tbody tr').each(function(index) {
            $(this).find('select, input').each(function() {
                let name = $(this).attr('name');
                if (name) {
                    name = name.replace(/\[\d+\]/, '[' + index + ']');
                    $(this).attr('name', name);
                }
            });
        });
        itemIndex = $('#items-tbody tr').length;
    }

    function updateRowEvents() {
        // Reinitialize any events that need to be bound to new rows
    }

    // Initialize calculations on page load
    calculateTotals();

    // Form validation
    $('#quotation-form').on('submit', function(e) {
        let hasValidItems = false;
        
        $('.item-row').each(function() {
            let description = $(this).find('.item-description').val();
            let qty = $(this).find('.item-qty').val();
            let price = $(this).find('.item-price').val();
            
            if (description && qty && price) {
                hasValidItems = true;
                return false; // break
            }
        });
        
        if (!hasValidItems) {
            alert('กรุณาเพิ่มรายการสินค้า/บริการอย่างน้อย 1 รายการ');
            e.preventDefault();
            return false;
        }
    });
});
