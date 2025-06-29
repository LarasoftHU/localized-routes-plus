#!/bin/bash

echo "🧪 Tesztelés kezdése..."

echo "📋 Függőségek ellenőrzése..."
composer show -D

echo "✅ Kódstílus ellenőrzés..."
vendor/bin/pint --test

echo "🔍 Statikus elemzés..."
vendor/bin/phpstan analyse --no-progress

echo "🎯 Tesztek futtatása..."
vendor/bin/pest

echo "✨ Minden ellenőrzés kész!" 