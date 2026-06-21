@echo off
:: ============================================================
::  RFD Laundry Management System - Launcher (double-click)
::  Menjalankan start.ps1 via PowerShell
:: ============================================================
title RFD Laundry - Launcher
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0start.ps1"
