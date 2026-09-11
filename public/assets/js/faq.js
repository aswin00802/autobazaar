
         document.querySelectorAll('.faq-item').forEach(item => {
               item.addEventListener('click', () => {
                  const isOpen = item.classList.contains('open');

                  document.querySelectorAll('.faq-item').forEach(otherItem => {
                     if (otherItem !== item) {
                           otherItem.classList.remove('open');
                           otherItem.querySelector('.faq-answer').style.display = 'none';
                           otherItem.querySelector('.toggle-symbol').textContent = '+';
                     }
                  });

                  item.classList.toggle('open', !isOpen);

                  const answer = item.querySelector('.faq-answer');
                  const symbol = item.querySelector('.toggle-symbol');
                  if (isOpen) {
                     answer.style.display = 'none';
                     symbol.textContent = '+';
                  } else {
                     answer.style.display = 'block';
                     symbol.textContent = '-';
                  }
               });
         });
      