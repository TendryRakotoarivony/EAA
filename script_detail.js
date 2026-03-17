document.addEventListener('DOMContentLoaded', function() {
  // Initialize performance bars with existing data
  for (let i = 1; i <= 6; i++) {
    const input = document.querySelector(`input[name="perf_${i}_note"]`);
    const bar = document.querySelector(`#bar_${i}`);
    if (input && bar && input.value) {
      const value = parseFloat(input.value) || 0;
      const percentage = (value / 5) * 100;
      bar.style.width = percentage + '%';
    }
  }
  computeAverage();
});