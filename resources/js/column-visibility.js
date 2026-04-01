document.addEventListener('DOMContentLoaded', function () {
  // Toggle column visibility dropdown
  document.querySelectorAll('.column-visibility-btn').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.stopPropagation()
      const menu = this.nextElementSibling
      document.querySelectorAll('.column-visibility-menu').forEach(function (m) {
        if (m !== menu) m.classList.add('hidden')
      })
      menu.classList.toggle('hidden')
    })
  })

  document.addEventListener('click', function () {
    document.querySelectorAll('.column-visibility-menu').forEach(function (m) {
      m.classList.add('hidden')
    })
  })

  document.querySelectorAll('.column-visibility-menu').forEach(function (menu) {
    menu.addEventListener('click', function (e) {
      e.stopPropagation()
    })
  })

  // Load saved column visibility and apply
  document.querySelectorAll('.column-toggle').forEach(function (cb) {
    const storageKey = cb.dataset.storage
    const tableId = cb.dataset.table
    const columnId = cb.dataset.column

    const saved = localStorage.getItem(storageKey)
    const hidden = saved ? JSON.parse(saved) : []

    if (hidden.includes(columnId)) {
      cb.checked = false
      toggleColumn(tableId, columnId, false)
    } else {
      cb.checked = true
      toggleColumn(tableId, columnId, true)
    }

    cb.addEventListener('change', function () {
      const checkboxes = document.querySelectorAll(
        `.column-toggle[data-table="${tableId}"]`
      )
      const hidden = []
      checkboxes.forEach(function (c) {
        if (!c.checked) hidden.push(c.dataset.column)
      })
      localStorage.setItem(storageKey, JSON.stringify(hidden))
      toggleColumn(tableId, columnId, cb.checked)
    })
  })

  function toggleColumn (tableId, columnId, visible) {
    const table = document.getElementById(tableId)
    if (!table) return
    const cells = table.querySelectorAll(`[data-column="${columnId}"]`)
    cells.forEach(function (cell) {
      cell.style.display = visible ? '' : 'none'
    })
  }
})
