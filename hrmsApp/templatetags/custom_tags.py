from django import template
from datetime import datetime, date

register = template.Library()

@register.filter
def range_filter(start, end):
    return range(int(start), int(end) +1)

@register.filter
def to_int(value):
    return int(value)

@register.filter
def format_date(value):
    if isinstance(value, datetime):
        return value.strftime("%Y-%m-%d")
    elif isinstance(value, date):
        return value.strftime("%Y-%m-%d")
    return value

@register.filter
def calculate_age(dob):
    if dob:
        today = datetime.today().date()
        age = today.year - dob.year - ((today.month, today.day) < (dob.month, dob.day))
        return age
    return None

@register.filter
def time(value):
    try:
        time_obj = datetime.strptime(value, '%H:%M')
        return time_obj.strftime('%I:%M %p')
    except ValueError:
        return value
       